<?php

namespace Betalabs\Engine\Configs;

use Betalabs\Engine\Configs\Exceptions\AuthInternalNotDefinedException;
use Betalabs\Engine\Configs\Exceptions\AuthNotDefinedException;

class Auth extends AbstractProvider
{

    /**
     * Return accessToken
     *
     * @return string
     * @throws \Betalabs\Engine\Configs\Exceptions\AuthInternalNotDefinedException
     * @throws \Betalabs\Engine\Configs\Exceptions\AuthNotDefinedException
     * @throws \Betalabs\Engine\Configs\Exceptions\ConfigDoesNotExistException
     */
    public function accessToken()
    {
        if (!isset($this->environmentNode()->accessToken)) {
            throw new AuthInternalNotDefinedException(
                'accessToken does not exist in configuration file'
            );
        }

        return $this->environmentNode()->accessToken;
    }

    /**
     * Catch auth node
     *
     * @return \SimpleXMLElement
     * @throws \Betalabs\Engine\Configs\Exceptions\AuthNotDefinedException
     * @throws \Betalabs\Engine\Configs\Exceptions\ConfigDoesNotExistException
     */
    protected function environmentNode()
    {
        if (!isset($this->reader->load()->auth)) {
            throw new AuthNotDefinedException(
                'auth node does not exist in configuration file'
            );
        }

        return $this->reader->load()->auth;
    }

}