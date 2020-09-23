<?php


namespace Yetione\GatewayRequest\Auth;


use Yetione\GatewayRequest\Exceptions\GatewayRequestInvalid;
use Yetione\GatewayRequest\Providers\GatewayRequestProvider;
use BadMethodCallException;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FromGatewayGuard implements Guard
{
    /**
     * @var Authenticatable|null
     */
    protected ?Authenticatable $user = null;

    protected RequestHeaderUserProvider $provider;

    protected Request $request;

    protected GatewayRequestProvider $gatewayRequestProvider;


    public function __construct(Request $request, GatewayRequestProvider $gatewayRequestProvider, RequestHeaderUserProvider $provider)
    {
        $this->request = $request;
        $this->gatewayRequestProvider = $gatewayRequestProvider;
        $this->provider = $provider;
    }

    public function check()
    {
        return !is_null($this->user());
    }

    public function guest()
    {
        return !$this->check();
    }

    public function user()
    {
        if (null !== $this->user) {
            return $this->user;
        }
        try {
//            $gatewayUrl = $this->gatewayRequestProvider->getGatewayUrl();
            $jwtPayload = $this->gatewayRequestProvider->getPayload();
            $userData = $this->gatewayRequestProvider->getUser();
        } catch (GatewayRequestInvalid $e) {
            Log::error('Auth failed!. '.$e->getMessage(), ['code'=>$e->getCode()]);
            return null;
        }
        if (!isset($userData['id']) || $jwtPayload->get('sub') !== $userData['id']) {
            Log::error('Auth failed! User ID and token subject must be equals.',
                ['sub'=>$jwtPayload->get('sub'), 'user_id'=>$userData['id']]);
            return null;
        }
        $this->user = $this->provider->retrieveUser($userData, $jwtPayload);
        Log::debug('Test can.', ['c'=>$this->user->token()->can('upload-files'), 'f'=>$this->user->token()->scopes]);
        return $this->user;
    }

    public function id()
    {
        if ($this->user()) {
            return $this->user()->getAuthIdentifier();
        }
        return null;
    }

    public function validate(array $credentials = [])
    {
        throw new BadMethodCallException('Method not implemented.');
    }

    public function setUser(Authenticatable $user)
    {
        $this->user = $user;
        return $this;
    }
}
