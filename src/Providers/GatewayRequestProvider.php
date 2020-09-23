<?php


namespace Yetione\GatewayRequest\Providers;


use Yetione\GatewayRequest\Enums\GatewayHttpHeaders;
use Yetione\GatewayRequest\Exceptions\GatewayRequestInvalid;
use Exception;
use Illuminate\Http\Request;
use Lcobucci\JWT\Parser as JwtParser;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Factory as JwtPayloadFactory;
use Tymon\JWTAuth\Http\Parser\Parser as JwtReader;
use Tymon\JWTAuth\Payload;
use Yetione\Json\Exceptions\JsonException;
use Yetione\Json\Json;

class GatewayRequestProvider
{

    protected Request $request;

    protected JwtReader $jwtReader;

    protected JwtParser $jwtParser;

    protected JwtPayloadFactory $jwtPayloadFactory;

    public function __construct(
        Request $request,
        JwtReader $jwtReader,
        JwtParser $jwtParser,
        JwtPayloadFactory $jwtPayloadFactory
    ) {
        $this->request = $request;
        $this->jwtReader = $jwtReader;
        $this->jwtParser = $jwtParser;
        $this->jwtPayloadFactory = $jwtPayloadFactory;
    }

    /**
     * @return array
     * @throws GatewayRequestInvalid
     */
    public function getUser(): array
    {
        if (!$this->request->headers->has(GatewayHttpHeaders::USER) ||
            empty($headerValue = $this->request->header(GatewayHttpHeaders::USER))) {
            throw new GatewayRequestInvalid('User header is required.');
        }
        if (false === ($decodedHeader = base64_decode($headerValue))) {
            throw new GatewayRequestInvalid('User header has invalid value.');
        }
        try {
            $userData = Json::decode($decodedHeader, true);
        } catch (JsonException $e) {
            throw new GatewayRequestInvalid($e->getMessage(), $e->getCode(), $e);
        }
        return $userData;
    }

    /**
     * @return Payload
     * @throws GatewayRequestInvalid
     */
    public function getPayload(): Payload
    {
        if (null === ($parsedToken = $this->jwtReader->parseToken())) {
            throw new GatewayRequestInvalid('Authorization token is required.');
        }
        try {
            $token = $this->jwtParser->parse($parsedToken);
        } catch (Exception $e) {
            throw new GatewayRequestInvalid($e->getMessage(), $e->getCode(), $e);
        }
        $claims = array_map(static function ($claim) {
            return is_object($claim) ? $claim->getValue() : $claim;
        }, $token->getClaims());
        try {
            $payload = $this->jwtPayloadFactory->setRefreshFlow(false)->customClaims($claims)->make();
        } catch (JWTException $e) {
            throw new GatewayRequestInvalid($e->getMessage(), $e->getCode(), $e);
        }
        return $payload;
    }

    /**
     * @return string
     * @throws GatewayRequestInvalid
     */
    public function getGatewayUrl(): string
    {
        if (!$this->request->headers->has(GatewayHttpHeaders::GATEWAY_URL) ||
            empty($gatewayUrl = $this->request->header(GatewayHttpHeaders::GATEWAY_URL))) {
            throw new GatewayRequestInvalid('Gateway URL header is required.');
        }
        return $gatewayUrl;
    }
}
