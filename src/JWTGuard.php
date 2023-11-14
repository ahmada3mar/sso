<?php

namespace  Hyperpay\SSO;

use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Http;
use Tymon\JWTAuth\JWTGuard as Guard;

class JWTGuard extends Guard
{
    /**
     * Attempt to authenticate the user using the given credentials and return the token.
     *
     * @param  array  $credentials
     * @param  bool  $login
     * @return bool|string
     */
    public function attempt(array $credentials = [], $login = true)
    {
        $result = $this->provider->login($credentials);
        if (!$result) {
            return false;
        }
        $this->setToken($result['token']);
        $this->iniUser($result['user']);
        return $result['token'];
    }


    public function user()
    {

        if (!is_null($this->user)) {
            return $this->user;
        }

        if ($this->jwt->setRequest($this->request)->getToken() && $this->jwt->check()) {
            $userPayload = $this->jwt->payload()->toArray();

            $userDetails = Http::withHeaders(['project_name' => \config('sso.project_name')])->withToken($this->jwt->getToken())
                ->get($userPayload['iss'] . "/api/user/" . $userPayload['sub'])->json("data");

            $this->user = new User((array) $userDetails);
            $this->user->permissions = \collect(\array_map(function ($p) {
                return new Permission($p);
            }, $userDetails['permissions']));
            return $this->user;
        }
    }

    protected function iniUser($data)
    {
        $user = new User($data);
        $user->permissions = Permission::hydrate($user->permissions);
        $this->setUser($user);
    }

    public function setUser($user)
    {

        $this->user = $user;
    }
}
