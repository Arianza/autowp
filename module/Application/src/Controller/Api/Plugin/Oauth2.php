<?php

namespace Application\Controller\Api\Plugin;

use InvalidArgumentException;
use Laminas\ApiTools\OAuth2\Provider\UserId\UserIdProviderInterface;
use Laminas\Mvc\Controller\Plugin\AbstractPlugin;
use OAuth2\Request as OAuth2Request;
use OAuth2\Server as OAuth2Server;
use RuntimeException;

use function call_user_func;
use function get_class;
use function gettype;
use function is_callable;
use function is_object;
use function sprintf;

class Oauth2 extends AbstractPlugin
{
    protected OAuth2Server $server;

    /** @var callable Factory for generating an OAuth2Server instance. */
    protected $serverFactory;

    protected UserIdProviderInterface $userIdProvider;

    /**
     * Constructor
     */
    public function __construct(OAuth2Server $serverFactory, UserIdProviderInterface $userIdProvider)
    {
        if (! is_callable($serverFactory)) {
            throw new InvalidArgumentException(sprintf(
                'OAuth2 Server factory must be a PHP callable; received %s',
                is_object($serverFactory) ? get_class($serverFactory) : gettype($serverFactory)
            ));
        }
        $this->serverFactory  = $serverFactory;
        $this->userIdProvider = $userIdProvider;
    }

    /**
     * @return mixed|null
     */
    public function __invoke()
    {
        $token = $this->getOAuth2Server()->getAccessTokenData(OAuth2Request::createFromGlobals());
        return $token ? $token['user_id'] : null;
    }

    /**
     * Retrieve the OAuth2\Server instance.
     *
     * If not already created by the composed $serverFactory, that callable
     * is invoked with the provided $type as an argument, and the value
     * returned.
     */
    private function getOAuth2Server(): OAuth2Server
    {
        if ($this->server instanceof OAuth2Server) {
            return $this->server;
        }

        $server = call_user_func($this->serverFactory);
        if (! $server instanceof OAuth2Server) {
            throw new RuntimeException(sprintf(
                'OAuth2\Server factory did not return a valid instance; received %s',
                is_object($server) ? get_class($server) : gettype($server)
            ));
        }
        $this->server = $server;
        return $server;
    }
}
