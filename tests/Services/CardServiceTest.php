<?php

namespace Tests\Services;

use App\Services\ApiRequests\PreorderingRequest;
use App\Services\CardService;
use Illuminate\Http\Request;
use Laravel\Lumen\Testing\DatabaseTransactions;
use Laravel\Lumen\Testing\WithoutMiddleware;
use Tests\TestCase;

/**
 * Class CardServiceTest
 * @package Tests\Services
 */
class CardServiceTest extends TestCase
{
    use WithoutMiddleware, DatabaseTransactions;

    /**
     * @covers \App\Services\CardService::checkCard
     */
    public function testCheckCardSuccess()
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

        $result = (new CardService($mock))->checkCard($request);
        $this->assertIsArray($result);
        $this->assertArrayHasKey('success', $result);
        $this->assertArrayHasKey('data', $result);

        if (array_key_exists('data', $result)) {
            $this->assertArrayHasKey('discount', $result['data']);
            $this->assertArrayHasKey('phone', $result['data']);
        }
    }

    /**
     * @covers \App\Services\CardService::checkCard
     */
    public function testCheckCardFails()
    {
        $request = new Request([
            'cardCode'   => '123123'
        ]);

        $mock = \Mockery::mock('App\Services\ApiRequests\PreorderingRequest');
        $mock->shouldReceive('checkCard')
            ->andReturn([
                "CountAvailable" => 0,
                "CountOveral" => 0,
                "Discount" => 0,
                "IsBirthdayDiscountAvailable" => false,
                "PhoneNumber" => "",
                "Result" => "Error. Card not found!"
            ]);

        $result = (new CardService($mock))->checkCard($request);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('success', $result);
    }

    /**
     * @covers \App\Services\CardService::prepareDiscountCode
     */
    public function testPrepareDiscountCode()
    {
        $employeeCard = '12135123';
        $doctorCard = '325335';

        $cardService = new CardService(new PreorderingRequest());

        $employeeDiscountCode = $cardService->prepareDiscountCode($employeeCard);
        $doctorDiscountCode = $cardService->prepareDiscountCode($doctorCard);
        $this->assertSame($employeeDiscountCode, 'SV50');
        $this->assertSame($doctorDiscountCode, 'VR50');
    }
}
