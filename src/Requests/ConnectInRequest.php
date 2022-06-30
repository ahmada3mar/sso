<?php

namespace Hyperpay\ConnectIn\Requests;

use Hyperpay\ConnectIn\ConnectIn;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class ConnectInRequest extends FormRequest
{
    /**
     * prepare request for validation
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        $data = $this->request->all();
        $parsedData = $this->parse_query(request()->getContent());

        $this->replace($parsedData);

        if (isset($this->customParameters['categories'])) {
            $customParams = $this->customParameters;
            $customParams['categories'] = json_decode($this->customParameters['categories'], true);
            $this->merge([
                'customParameters' => $customParams,
            ]);
        }
    }
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {

        return [
            'authentication_entityId' => [
                'required',
                Rule::exists('merchants', 'authentication_entityId')->where(function ($query) {
                    $query->where([
                        'authentication_password' => $this->authentication_password,
                        'authentication_userId' => $this->authentication_userId
                    ]);
                })
            ],
            'amount' => 'required|numeric',
            'currency' => 'required',
        ];
    }

    /**
     * set custom validation error message for passoerd , user id and entity id
     */
    public function messages()
    {
        return [
            'authentication_entityId.exists' => 'entity id, password, or user id is invalid',
        ];
    }


    public function failedValidation(Validator $validator)
    {
        $data = $validator->valid();
        $connectIn = new ConnectIn($data);
        $connectIn->errors($validator->errors()->all());

        throw new HttpResponseException($connectIn->response(ConnectIn::BAD_REQUEST));
    }

    public function response($code, $data = [])
    {
        $data = array_merge($this->all(), $data);
        $connectIn = new ConnectIn($data);
        return $connectIn->response($code);
    }
}
