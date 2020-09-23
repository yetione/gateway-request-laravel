<?php


namespace Yetione\GatewayRequest\Auth;


use Yetione\GatewayRequest\GatewayUser;
use BadMethodCallException;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Payload;

class RequestHeaderUserProvider implements UserProvider
{
    protected Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function retrieveUser(array $userData, Payload $jwtPayload)
    {
        $user = new GatewayUser($userData);
        $user->setTokenPayload($jwtPayload);
        return $user;
    }

    public function retrieveById($identifier)
    {
        throw new BadMethodCallException('Method not implemented.');
    }

    public function retrieveByToken($identifier, $token)
    {
        throw new BadMethodCallException('Method not implemented.');
    }

    public function updateRememberToken(Authenticatable $user, $token)
    {
        throw new BadMethodCallException('Method not implemented.');
    }

    public function retrieveByCredentials(array $credentials)
    {
        throw new BadMethodCallException('Method not implemented.');
    }

    public function validateCredentials(Authenticatable $user, array $credentials)
    {
        throw new BadMethodCallException('Method not implemented.');
    }
}
