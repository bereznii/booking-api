<?php

namespace App\Http\Validation;

use Illuminate\Http\Request;

/**
 * Trait CardValidation
 * @package App\Http\Validation
 */
trait CardValidation
{
    /**
     * @param Request $request
     * @return array
     */
    public function checkCardRequest(Request $request): array
    {
        return [
            $request,
            [
                'cardCode' => 'required|string'
            ]
        ];
    }
}
