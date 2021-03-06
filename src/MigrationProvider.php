<?php

namespace Betalabs\Engine;

interface MigrationProvider
{

    /**
     * Run database migration
     *
     * @return \Betalabs\Engine\Requests\BootResponse
     */
    public function run();

}