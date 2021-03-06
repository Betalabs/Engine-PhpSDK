<?php

namespace Betalabs\Engine\Requests\Methods;

class Get extends Request
{

    /**
     * @param $path
     *
     * @return mixed
     * @throws \Betalabs\Engine\Auth\Exceptions\TokenExpiredException
     * @throws \Betalabs\Engine\Auth\Exceptions\UnauthorizedException
     * @throws \Betalabs\Engine\Configs\Exceptions\ClientNotDefinedException
     * @throws \Betalabs\Engine\Configs\Exceptions\ConfigDoesNotExistException
     * @throws \Betalabs\Engine\Configs\Exceptions\PropertyNotFoundException
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     * @throws \ReflectionException
     */
    public function send($path)
    {

        return $this->processContents(
            $this->client->get(
                $this->uri($path),
                $this->buildOptions()
            )
        );

    }

}