<?php

namespace Tests;

use Laravel\Lumen\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * Creates the application.
     *
     * @return \Laravel\Lumen\Application
     */
    public function createApplication()
    {
        return require __DIR__.'/../bootstrap/app.php';
    }

    /**
     * @return string
     */
    protected function getBasicAuthUsername(): string
    {
        return config('api-auth.username');
    }

    /**
     * @return string
     */
    protected function getBasicAuthPassword(): string
    {
        return config('api-auth.password');
    }
}
