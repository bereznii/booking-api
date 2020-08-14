<?php

namespace Tests\Services;

use App\Services\LogService;
use Laravel\Lumen\Testing\DatabaseTransactions;
use Laravel\Lumen\Testing\WithoutMiddleware;
use Tests\TestCase;

/**
 * Class LogServiceTest
 * @package Tests\Services
 */
class LogServiceTest extends TestCase
{
    use WithoutMiddleware, DatabaseTransactions;

    /**
     * @covers \App\Services\LogService::setContext
     */
    public function testSetContext()
    {
        $result = (new LogService())->setContext('context');

        $this->assertInstanceOf(LogService::class, $result);
    }

    /**
     * @covers \App\Services\LogService::setMessage
     */
    public function testSetMessage()
    {
        $result = (new LogService())->setMessage('message');

        $this->assertInstanceOf(LogService::class, $result);
    }

    /**
     * @covers \App\Services\LogService::setExtra
     */
    public function testSetExtra()
    {
        $result = (new LogService())->setMessage('extra');

        $this->assertInstanceOf(LogService::class, $result);
    }
}
