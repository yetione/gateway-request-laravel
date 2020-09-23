<?php


namespace Yetione\GatewayRequest;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Access\Gate;use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Laravel\Passport\HasApiTokens;
use Tymon\JWTAuth\Payload;
use Laravel\Passport\Token as AccessToken;

class GatewayUser extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, HasApiTokens;

    protected $fillable = [
        'id', 'name', 'email', 'plan_id', 'project'
    ];

    protected $hidden = [
        'password',
    ];

    public $incrementing = false;

    protected $keyType = 'string';

    protected ?object $projectObject = null;

    public function setTokenPayload(Payload $payload)
    {
        $this->accessToken = new AccessToken([
            'id'=>$payload->get('aud'),
            'user_id'=>$payload->get('sub'),
            'scopes'=>$payload->get('scopes')
        ]);
        return $this;
    }

    public function getProjectAttribute($value): object
    {
        if (null === $this->projectObject) {
            $this->projectObject = $this->createProjectClass($value);
        }
        return (object) $value;
    }

    public function can($ability, $arguments = [])
    {
        return app(Gate::class)->forUser($this)->check($ability, $arguments);
    }

    /**
     * @param array $value
     * @return object
     */
    protected function createProjectClass(array $value): object
    {
        return new class ($value){
            public string $id;

            public string $owner_id;

            public array $data;

            public string $name;

            public string $shopify_domain;

            public function __construct(array $project)
            {
                $this->id = $project['id'];
                $this->owner_id = $project['owner_id'];
                $this->name = $project['name'];
                $this->shopify_domain = $project['shopify_domain'];
                $this->data = $project['data'] ?? [];
            }
        };
    }

}
