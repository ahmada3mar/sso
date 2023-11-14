<?php

namespace App\Guard;


use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\UserProvider as provider;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;


class UserProvider implements provider
{
    private $user, $ssoRequest;

    public function __construct()
    {
        $this->ssoRequest = $this->ssoRequest();
    }

    public function retrieveById($identifier)
    {

        $user =  $this->ssoRequest->post("/user/$identifier")->json();
        $this->user = new User($user);
    }

    public function retrieveByToken($identifier, $token)
    {
        // $token = $this->token->with('user')->where($identifier, $token)->first();

        // return $token && $token->user ? $token->user : null;
    }

    public function updateRememberToken(Authenticatable $user, $token)
    {
        // update via remember token not necessary
    }

    public function login($credentials)
    {
        return $this->ssoRequest->post("/login", $credentials)->json();
    }

    public function retrieveByCredentials(array $credentials)
    {
        $response =  $this->ssoRequest->post("/login", $credentials)->json();
        $this->user = new User($response['user']);
    }

    public function validateCredentials(Authenticatable $user, array $credentials)
    {
        $r =$this->ssoRequest->post("/validateCredentials", $credentials)->json();
        return dd($r); 
    }

    public function ssoRequest()
    {
        return Http::baseUrl("http://127.0.0.1:8000/api");
    }
}
