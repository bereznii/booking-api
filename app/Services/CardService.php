<?php

namespace App\Services;

use App\Services\ApiRequests\PreorderingRequest;
use Illuminate\Http\Request;

/**
 * Class CardService
 * @package App\Services
 */
class CardService
{
    /**
     * @var PreorderingRequest
     */
    private PreorderingRequest $preorderingRequest;

    /**
     * CardService constructor.
     * @param PreorderingRequest $preorderingRequest
     */
    public function __construct(PreorderingRequest $preorderingRequest)
    {
        $this->preorderingRequest = $preorderingRequest;
    }

    /**
     * @param Request $request
     * @return array
     */
    public function checkCard(Request $request): array
    {
        $data = ['Cardcode' => $request->input('cardCode')];

        $discountData = $this->preorderingRequest->checkCard($data);

        if (strpos($discountData['Result'], 'Success') === false) {
            return ['success' => false];
        }

        return [
            'success' => true,
            'data' => [
                'discount' => $discountData['Discount'],
                'phone' => $discountData['PhoneNumber']
            ]
        ];
    }

    /**
     * If card code begins with 1, it is employee card, otherwise - doctor card.
     *
     * @param string $clientCard
     * @return string
     */
    public static function prepareDiscountCode(string $clientCard): string
    {
        return preg_match('/^1/', $clientCard) === 1
            ? '<<EMPLOYEE_CODE>>'
            : '<<DOCTOR_CODE>>';
    }

    /**
     * @param string $clientCard
     * @return bool
     */
    public function checkCardIsValid(string $clientCard): bool
    {
        $data = ['Cardcode' => $clientCard];

        $discountData = $this->preorderingRequest->checkCard($data);

        return $discountData['Result'] === 'Success. Card is ready to use!';
    }
}
