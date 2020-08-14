<?php

namespace App\Http\Validation;

use App\Models\UniqueTests;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Trait ReservationValidation
 * @package App\Http\Validation
 */
trait ReservationValidation
{
    /**
     * @param Request $request
     * @return array
     */
    public function getFreeIntervalsRequest(Request $request): array
    {
        return [
            $request,
            [
                'startDate' => 'filled|date|date_format:"Y-m-d',
                'days' => 'filled|integer|gte:0|lte:60',
                'centerId' => [
                    'required',
                    'integer',
                    Rule::in(UniqueTests::getUniqueSpermogramCentersIds()),
                ]
            ]
        ];
    }

    /**
     * @param Request $request
     * @return array
     */
    public function storeIntervalRequest(Request $request): array
    {
        return [
            $request,
            [
                'middleName' => 'filled|string|max:100',
                'firstName' => 'required|string|max:100',
                'lastName' => 'required|string|max:100',
                'doctor' => 'filled|string|max:255',
                'card' => 'filled|string|max:100',
                'email' => 'required|email|max:100',
                'phone' => 'required|string|max:100',
                'birthDate' => 'required|date|date_format:"Y-m-d"',
                'sex' => 'required|integer|in:0,1',
                'centerId' => [
                    'required',
                    'integer',
                    Rule::in(UniqueTests::getUniqueSpermogramCentersIds()),
                ],
                'testCode' => 'required|string|in:1111',
                'appointmentTime' => [
                    'required',
                    'date',
                    'date_format:"Y-m-d H:i"',
                    Rule::unique('reservations', 'appointment_time')->where(function ($query) use ($request) {
                        return $query->where('center_original_id', $request->get('centerId'));
                    }),
                ]
            ],
            [
                'appointmentTime.unique' => 'The appointment time for this center has already been taken.'
            ]
        ];
    }

    /**
     * @param Request $request
     * @return array
     */
    public function getExistingReservationsRequest(Request $request): array
    {
        return [
            $request,
            [
                'startDate' => 'filled|date|date_format:"Y-m-d',
                'days' => 'filled|integer|gte:0|lte:60',
                'centerId' => [
                    'required',
                    'integer',
                    Rule::in(UniqueTests::getUniqueSpermogramCentersIds()),
                ]
            ]
        ];
    }

    /**
     * @param Request $request
     * @return array
     */
    public function getExistingReservationsGroupedRequest(Request $request): array
    {
        return [
            $request,
            [
                'date' => 'filled|date|date_format:"Y-m-d'
            ]
        ];
    }
}
