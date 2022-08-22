<?php

namespace Hyperpay\ConnectIn;

use Carbon\Carbon;
use Hyperpay\ConnectIn\Models\Transaction;
use Illuminate\Support\Facades\Http;


/**
 * Helper class for generating connectIn responses/errors
 */
class ConnectIn
{
    private $data;
    private $redirect = [];
    private $errors = [];

    const CREATED = 201;
    const SUCCESSFUL = 200;
    const BAD_REQUEST = 400;



    public function __construct($data = null)
    {
        $this->data = $data;
    }

    /**
     * maping ACI response code
     *
     * @param int $code
     * @return array<string , string>
     */

    private function ACIResponse($code)
    {
        $list = [
            '200' => [
                'aci_code' => '000.000.000',
                'description' => 'Transaction Successful',
            ],
            '201' => config('connect-in.default_response'),

            '400' => [
                'aci_code' => '800.100.156',
                'description' => 'Invalid or missing parameter',
            ],
            '401' => [
                'aci_code' => '800.100.152',
                'description' => 'You are not authenticated to perform the requested action',
            ],
            '404' => [
                'aci_code' => '800.100.100',
                'description' => 'Not Found',
            ],
            '500' => [
                'aci_code' => '800.100.100',
                'description' => 'Internal Server Error',
            ],
            '503' => [
                'aci_code' => '800.100.100',
                'description' => 'Service Unavailable',
            ],
            '504' => [
                'aci_code' => '900.100.300',
                'description' => 'Gateway Timeout',
            ],
            '1' => [
                'aci_code' => '800.400.500',
                'description' => 'Waiting for confirmation of non-instant payment. Denied for now.'
            ],
            '2' => [
                'aci_code' => '000.000.000',
                'description' => 'Transaction succeeded',
            ],
            '3' => [
                'aci_code' => '000.200.102',
                'description' => 'successfully deleted checkout',
            ],
            '4' => [
                'aci_code' => '800.100.152',
                'description' => 'transaction declined by authorization system',
            ],
            '5' => [
                'aci_code' => '100.396.201',
                'description' => 'Cancelled by merchant',
            ],
        ];


        return [
            "code" => $list[$code]['aci_code'],
            "description" => $list[$code]['description']
        ];
    }

    /**
     * Send notify request to ACI to update transaction status.
     *
     * @param Transaction $transaction
     * @param int $code
     *
     * @return \Illuminate\Http\Client\Response $response
     */

    public function aciNotification(Transaction $transaction, $code)
    {
        $status = $this->ACIResponse($code);
        $aciCode = $status['code'];

        $resultDetails = $this->getResultDetails($code);
        $dec = $resultDetails['ExtendedDescription'] ?? $status['description'];

        $url = "{$transaction->notificationUrl}&status=$aciCode&resultDetails.extendedDescription=$dec&resultDetails.acquirerResponse=$code";

        $data = $this->aciNotificationUrl($url, $transaction->merchant);
        $response =  Http::post($data);

        $Aci_log = [
            'request'  => [
                'URL' =>  $url,
                'data' => $data
            ],
            'response' => $response->json()
        ];

        $transaction->mongoLog->push('ACI', $Aci_log);

        return $response;
    }

    /**
     * Generate ACI signature.
     *
     * @param string $notificationUrl
     * @param App\Models\Merchant $merchant
     *
     * @return string
     */
    private function aciNotificationUrl($notificationUrl, $merchant)
    {
        $url = parse_url($notificationUrl);
        parse_str($url['query'], $query);
        ksort($query);

        $params = [];
        foreach ($query as $key => $value) {
            $key = str_replace('_', '.', $key);
            $params[] = $key . '=' . $value;
        }

        return $notificationUrl . '&signature=' . hash_hmac('sha256', implode('|', $params), $merchant->aci_secret);
    }

    /**
     * set result details to ACI respone
     * this respone will showing to user
     *
     * @param int $code
     *
     * @return array $details
     */
    private function getResultDetails($code)
    {
        $details = [
            'responseCode' => $code,
        ];

        if (!empty($this->getErrors())) {
            $details['AcquirerResponse'] = "Validation Error";
            $details['ExtendedDescription'] =  json_encode([ "details" =>  $this->getErrors()]);
        }

        return $details;
    }

    /**
     * Return NDC number from notificationUrl.
     *
     * @param array $data
     * @return string ndcid
     */
    public function getNdc($data)
    {
        $notificationUrl = parse_url($data['notificationUrl'] ?? '');
        parse_str($notificationUrl['query'] ?? '', $queryParams);
        return $queryParams['ndcid'] ?? '';
    }

    /**
     * Return list of errors.
     *
     * @return array $erros
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Set list of errors.
     * @param array $erros
     * @return void
     */
    public function errors($data)
    {
        $this->errors = array_merge_recursive($this->errors, $data);
    }

    /**
     * Build redirect object for ACI.
     */
    private function getRedirect()
    {
        return [
            "url" =>   $this->redirect['url'],
            "method" =>   $this->redirect['method'] ?? 'GET',
            "parameters" =>  $this->redirect['parameters']
        ];
    }

    /**
     * Set redirect object.
     */
    public function setRedirect($redirect)
    {
        $this->redirect = $redirect;
    }

    /**
     *
     * process data and return respone to ACI
     * @param int $code
     *
     * @return \Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory
     */

    public function response($code)
    {
        $Aci_response = $this->ACIResponse($code);
        $resultDetails = $this->getResultDetails($code);

        $response = [
            "id" => $this->data['customParameters']['UUID'] ?? '',
            "paymentType" =>  $this->data['paymentType'] ?? '',
            "paymentBrand" =>  $this->data['paymentBrand'] ?? '',
            "amount" => $this->data['amount'] ?? '',
            "currency" =>  $this->data['currency'] ?? '',
            "descriptor" => $this->data['descriptor'] ?? '',
            "result" => $Aci_response,
            "resultDetails" =>  $resultDetails,
            "timestamp" =>  Carbon::now()->format('Y-m-d H:i:sP'),
            "ndc" => $this->getNdc($this->data)
        ];

        if (!empty($this->redirect)) {
            $response['redirect'] = $this->getRedirect();
        }


        return response($response);
    }
}
