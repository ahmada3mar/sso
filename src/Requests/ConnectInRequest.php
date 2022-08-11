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

    private function parse_query($str, $urlEncoding = true): array
    {
        $result = [];

        if ($str === '') {
            return $result;
        }

        if ($urlEncoding === true) {
            $decoder = function ($value) {
                return rawurldecode(str_replace('+', ' ', $value));
            };
        } elseif ($urlEncoding === PHP_QUERY_RFC3986) {
            $decoder = 'rawurldecode';
        } elseif ($urlEncoding === PHP_QUERY_RFC1738) {
            $decoder = 'urldecode';
        } else {
            $decoder = function ($str) {
                return $str;
            };
        }

        foreach (explode('&', $str) as $kvp) {
            $parts = explode('=', $kvp, 2);
            $key = $decoder($parts[0]);
            $value = isset($parts[1]) ? $decoder($parts[1]) : null;
            if (!isset($result[$key])) {
                $result[$key] = $value;
            } else {
                if (!is_array($result[$key])) {
                    $result[$key] = [$result[$key]];
                }
                $result[$key][] = $value;
            }
        }
        return $this->buildCartItems($result);
    }

    private function buildCartItems($parsedData)
    {
        $p1 = [];
        foreach ($parsedData as $key => $value) {
            if (\strpos($key, "cart") > -1) {
                $key = \explode(".", $key);
                $prop = \array_pop($key);
                $key = \implode(".", $key);
                $key .= "[" . $prop . "]";
            }
            $p1[$key] = $value;
        }
        \parse_str(\http_build_query($p1), $parsedData);
        return $parsedData;
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

        $errors = $validator->errors()->toArray();
        $errors = array_map(function($key, $value){
            return [
                'field' => $key,
                'error' => $value[0]
            ];
        } ,array_keys($errors) , $errors);
        $connectIn->errors($errors);

        throw new HttpResponseException($connectIn->response(ConnectIn::BAD_REQUEST));
    }

    public function response($code, $data = [])
    {
        $data = array_merge($this->all(), $data);
        $connectIn = new ConnectIn($data);
        return $connectIn->response($code);
    }

}
