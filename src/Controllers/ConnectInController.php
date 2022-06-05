<?php

namespace Hyperpay\ConnectIn\Controllers;

// use App\Helpers\ConnectIn;
// use App\Helpers\Kiosk;
// use App\Http\Controllers\Controller;
// use App\Http\Requests\ConnectInRequest;
// use App\Http\Requests\KioskRequest;
// use App\Jobs\MerchantNotification;
use App\Models\Brand;
// use App\Models\RequestTransaction;
// use App\Models\Transaction;
// use Exception;
use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class ConnectInController extends Controller
{

    /**
     *
     *  recive ACI request and return response to ACI
     *
     * @param App\Http\Requests\ConnectInRequest
     *
     * @return App\Http\Requests\ConnectInRequest::response
     */

    public function payment(Request $request)
    {
        return "ok";
        // conver request object to array of params
        $data = $request->all();
        extract($data);
        // Extract UUID from ACI request to asign it to a new transaction
        $data['UUID'] = $customParameters['UUID'];
        // $transaction = Transaction::create($data);

        // get invoice id (9 digits) from transaction
        $resonse_data = ['invoice_id' => $transaction->invoice_id];

        // return  $request->response(CREATED, $resonse_data);
    }

    /**
     *
     * Recive the request from Kiosks to get status of the transactions
     *
     * @param Request
     * @return App\Helpers\Kiosk::response
     */

    // public function inquiry(KioskRequest $request)
    // {
    //     $kiosk = new Kiosk();

    //     // get transacton from our DB(MYSql) based on $invoice_id , and handel errors if occur
    //     $transaction = $kiosk->getTransaction($request);

    //     return $kiosk->response(SUCCESS, $transaction);
    // }

    /**
     * recive request from kiosks to pay bills
     *
     * @param Reuest
     *
     * @return App\Helpers\Kiosk::response
     *
     */

    // public function confirm(KioskRequest $request)
    // {
    //     $kiosk = new Kiosk();
    //     $ConnectIn = new ConnectIn();

    //     // get transacton from our DB(MYSql) based on $invoice_id , and handel errors if occur
    //     $transaction = $kiosk->getTransaction($request);

    //     try {
    //         // send notification to ACI to update transaction status
    //         $ACI_res = $ConnectIn->aciNotification($transaction, SUCCESSFUL);
    //         $ACI_res = json_decode($ACI_res, true);

    //         // if transaction status updated to sucess
    //         if ($ACI_res['result']['code'] == '000.000.000') {

    //             $transaction->update(['status' => 2 , 'brand_uuid' => $request->brandId ]); // update status of transaction in our DB(MySql)

    //             if($request->requestId)
    //                 $transaction->requestTransaction()->create($request->all());

    //             // update merchant's order status
    //             $merchantNotification = new MerchantNotification($transaction);
    //             $this->dispatch($merchantNotification);

    //             return $kiosk->response(SUCCESS, null, $transaction->amount);

    //         } else {
    //             // set extra data in case ACI return code not equle 000.000.000 (success)
    //             $kiosk->setExtraData($ACI_res);
    //         }
    //     } catch (Exception $e) {
    //         Log::error($e->getMessage());
    //     }

    //     return $kiosk->response(SERVER_ERROR);
    // }

    // /**
    //  * get status of transaction based on merchant request id
    //  *
    //  * @param string $requestId
    //  *
    //  * @return App\Helpers\Kiosk::response
    //  */

    // public function status(Request $request , RequestTransaction $request_transaction)
    // {
    //     $kiosk = new Kiosk();

    //     $transaction = $request_transaction->transaction;

    //     // merchant can get status of his transacion only
    //     if(!in_array($request->brandId , $transaction->merchant->brands ?? []))
    //         return $kiosk->response(NOT_FOUND);


    //     /** if transaction exists on request_transactions table
    //      *  thats mean the transaction is success
    //     */
    //     return $kiosk->response(SUCCESS, $request_transaction->transaction);
    // }

}
