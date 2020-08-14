<?php

namespace App\Services\ApiRequests;

use App\Models\Reservation;
use App\Services\LogService;

/**
 * Class SiteRequest
 * @package App\Services\ApiRequests
 */
class SiteRequest
{
    /**
     * @var string
     */
    const URL = "";

    /**
     * @var string
     */
    const USER_AGENT = "Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/28.0.1500.95 Safari/537.36";

    /**
     * @return array|null
     */
    public function getCentersList(): ?array
    {
        $url = self::URL . '/<<URI>>';
        return $this->post($url, $this->collectUserData());
    }

    /**
     * @param Reservation $reservation
     * @return string|null
     */
    public function getWebOrderIdForReservation(Reservation $reservation): ?string
    {
        $url = self::URL . '/<<URI>>';

        $response = $this->post($url, $reservation->toArray());

        return $response['status'] === 'success'
            ? $response['id']
            : null;
    }

    /**
     * @param string $url
     * @param array $data
     * @return array|null
     */
    private function post(string $url, array $data): ?array
    {
        $options = [
            CURLOPT_POST            => 1,
            CURLOPT_POSTFIELDS      => json_encode($data, JSON_UNESCAPED_UNICODE),
            CURLOPT_HTTPHEADER      => [
                'Accept:application/json',
                'Content-Type:application/json'
            ],
            CURLOPT_USERAGENT       => self::USER_AGENT,
            CURLOPT_HTTPAUTH        => CURLAUTH_BASIC,
            CURLOPT_USERPWD         => config('site-auth.credentials'),
            CURLOPT_VERBOSE         => 1,
            CURLOPT_HEADER          => false,
            CURLOPT_SSL_VERIFYPEER  => false,
            CURLOPT_SSL_VERIFYHOST  => false,
            CURLINFO_HEADER_OUT     => 1,
            CURLOPT_RETURNTRANSFER  => 1,
            CURLOPT_TIMEOUT         => 60
        ];

        return $this->executeRequest($url, $options);
    }

    /**
     * @param string $url
     * @param array $options
     * @return array|null
     */
    private function executeRequest(string $url, array $options): ?array
    {
        $curl = curl_init($url);

        if (is_resource($curl)) {
            curl_setopt_array($curl, $options);
            $response = (string) curl_exec($curl);
            curl_close($curl);
        }

        $response ??= '';

        (new LogService('DEBUG'))->log(__METHOD__, 'Request to <<NAME>>', [
            'url' => $url,
            'data' => $options,
            'response' => $response
        ]);

        $response = json_decode($response, true);

        return $response;
    }

    /**
     * @return array
     */
    public function collectUserData(): array
    {
        return [];
    }
}
