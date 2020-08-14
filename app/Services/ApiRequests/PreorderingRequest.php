<?php

namespace App\Services\ApiRequests;

use App\Services\LogService;

/**
 * Class PreorderingRequest
 * @package App\Services\ApiRequests
 */
class PreorderingRequest
{
    /**
     * @var string
     */
    const URL = '';

    /**
     * @var string
     */
    const USER_AGENT = 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36';

    /**
     * @param array $data = ['Cardcode' = 'xxx']
     * @return array|null
     */
    public function checkCard(array $data): ?array
    {
        $url = self::URL . '/Checkcard';
        return $this->get($url, $data);
    }

    /**
     * @param string $url
     * @param array $params
     * @return array|null
     */
    private function get(string $url, array $params = []): ?array
    {
        $options = [
            CURLOPT_USERAGENT       => self::USER_AGENT,
            CURLOPT_HTTPAUTH        => CURLAUTH_NTLM,
            CURLOPT_USERPWD         => config('preordering-auth.credentials'),
            CURLOPT_VERBOSE         => true,
            CURLOPT_HEADER          => false,
            CURLOPT_SSL_VERIFYPEER  => false,
            CURLOPT_SSL_VERIFYHOST  => false,
            CURLINFO_HEADER_OUT     => true,
            CURLOPT_RETURNTRANSFER  => true,
            CURLOPT_TIMEOUT         => 60,
            CURLOPT_CUSTOMREQUEST   => 'GET'
        ];

        if (! empty($params)) {
            $url = $url . '?' . http_build_query($params);
        }

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
}
