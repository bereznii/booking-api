<?php

namespace Tests\Controllers;

use Illuminate\Testing\TestResponse;
use Laravel\Lumen\Testing\DatabaseTransactions;
use Laravel\Lumen\Testing\WithoutMiddleware;
use Tests\TestCase;

/**
 * Class CenterControllerTest
 * @package Tests\Controllers
 */
class CenterControllerTest extends TestCase
{
    use WithoutMiddleware, DatabaseTransactions;

    /**
     * @covers \App\Http\Controllers\CenterController::getSpermogramCenters
     */
    public function testGetSpermogramCenters()
    {
        $this->get('api/v1/spermogram/centers');

        $this->assertResponseOk();
        $this->seeJsonStructure([
            '*' => [
                'test' => [
                    'code', 'nameUa'
                ],
                'centers' => [
                    '*' => [
                        'id', 'nameUa'
                    ]
                ]
            ]
        ]);
    }
}
