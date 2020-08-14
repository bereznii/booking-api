<?php

namespace Tests\Controllers;

use App\Http\Controllers\CardController;
use App\Services\CardService;
use App\Services\LogService;
use Illuminate\Http\Request;
use Laravel\Lumen\Testing\DatabaseTransactions;
use Laravel\Lumen\Testing\WithoutMiddleware;
use Tests\TestCase;

/**
 * Class CardControllerTest
 * @package Tests\Controllers
 */
class CardControllerTest extends TestCase
{
    use WithoutMiddleware, DatabaseTransactions;

    /**
     * @covers \App\Http\Controllers\CardController::checkCard()
     */
    public function testCheckCard()
    {
        $request = new Request([
            'cardCode'   => '123123'
        ]);

        $mock = \Mockery::mock('App\Services\ApiRequests\PreorderingRequest');
        $mock->shouldReceive('checkCard')
            ->andReturn([
                "CountAvailable" => 3,
                "CountOveral" => 3,
                "Discount" => 50,
                "IsBirthdayDiscountAvailable" => true,
                "PhoneNumber" => "+380991234567",
                "Result" => "Success. Card is ready to use!"
            ]);

        $logService = new LogService();
        $cardService = new CardService($mock);
        $response = (new CardController($cardService, $logService))->checkCard($request);

        $result = $response->getData();

        $this->assertIsObject($result);
        $this->assertObjectHasAttribute('success', $result);
        $this->assertObjectHasAttribute('data', $result);

        if (isset($result->data)) {
            $this->assertObjectHasAttribute('discount', $result->data);
            $this->assertObjectHasAttribute('phone', $result->data);
        }
    }
}
