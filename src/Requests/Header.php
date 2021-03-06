<?php

namespace Betalabs\Engine\Requests;

use Betalabs\Engine\Auth\Header as AuthHeader;

class Header
{

    protected $header = [];

    /** @var \Betalabs\Engine\Auth\Header */
    protected $authorization;

    /**
     * Header constructor.
     * @param \Betalabs\Engine\Auth\Header $authorization
     */
    public function __construct(AuthHeader $authorization)
    {
        $this->authorization = $authorization;
        $this->header['Content-Type'] = 'application/json';
        $this->header['Accept'] = 'application/json';
    }


    /**
     * Define the header array
     *
     * @return array
     * @throws \Betalabs\Engine\Auth\Exceptions\TokenExpiredException
     * @throws \Betalabs\Engine\Auth\Exceptions\UnauthorizedException
     * @throws \Betalabs\Engine\Configs\Exceptions\ClientNotDefinedException
     * @throws \Betalabs\Engine\Configs\Exceptions\ConfigDoesNotExistException
     * @throws \Betalabs\Engine\Configs\Exceptions\PropertyNotFoundException
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     * @throws \ReflectionException
     */
    public function headers()
    {

        return array_merge(
                $this->header,
                $this->authorization->header()
            );

    }

    /**
     * Define authorization is required
     *
     * @return $this
     */
    public function mustAuthorize()
    {
        $this->authorization->mustAuthorize();
        return $this;
    }

    /**
     * Define authorization is not required
     *
     * @return $this
     */
    public function mustNotAuthorize()
    {
        $this->authorization->mustNotAuthorize();
        return $this;
    }

}