<?php

namespace App\Services\ApiRequests;

use App\Services\LogService;

/**
 * Class TeamsRequest
 * @package App\Services\ApiRequests
 */
class TeamsRequest
{
    /**
     * @var string
     */
    private string $appName;

    /**
     * @var array
     */
    private array $teams;

    /**
     * TeamsRequest constructor.
     */
    public function __construct()
    {
        $this->teams = config('teams');
        $this->appName = config('app.name');
        $this->appEnv = config('app.env');
    }

    /**
     * Send notification message to Microsoft Teams via Connector Incoming Webhook.
     * @param string $logType
     * @param string $message
     * @param string $context
     */
    public function sendNotification(string $logType, string $message, string $context): void
    {
        $message = $this->getNotificationMessage($logType, $message, $context);

        $this->post($message);
    }

    /**
     * @param string $logType
     * @param string $message
     * @param string $context
     * @return array
     */
    private function getNotificationMessage(string $logType, string $message, string $context): array
    {
        return [
            "@context" => "http://schema.org/extensions",
            "@type" => "MessageCard",
            "themeColor" => "fcb813",
            "summary" => 'Уведомление',
            'sections'   => [
                [
                    'activityTitle' => $this->appName,
                    'activitySubtitle' => 'Уведомление',
                    "activityImage" => $this->teams['activityImage'],
                    "facts" => [
                        [
                            "name" => "Окружение:",
                            "value" => $this->appEnv
                        ],
                        [
                            "name" => "Статус:",
                            "value" => $this->teams['statusMessages'][$logType]
                        ],
                        [
                            "name" => "Сообщение:",
                            "value" => $message
                        ],
                        [
                            "name" => "Контекст:",
                            "value" => $context
                        ],
                        [
                            "name" => "Время:",
                            "value" => date('l, Y-m-d H:i:s O')
                        ],
                    ],
                    'markdown' => true
                ]
            ]
        ];
    }

    /**
     * @param array $data
     * @return bool
     */
    private function post(array $data): bool
    {
        $json = json_encode($data);

        $options = [
            CURLOPT_POST            => true,
            CURLOPT_POSTFIELDS      => $json,
            CURLOPT_HTTPHEADER      => [
                'Content-Type:application/json',
                'Content-Length: ' . strlen($json)
            ],
            CURLOPT_RETURNTRANSFER  => true,
            CURLOPT_CONNECTTIMEOUT  => 5,
            CURLOPT_TIMEOUT         => 15
        ];

        return $this->executeRequest($this->teams['incoming_webhook_url'], $options);
    }

    /**
     * @param string $url
     * @param array $options
     * @return bool
     */
    private function executeRequest(string $url, array $options): bool
    {
        $curl = curl_init($url);

        if (is_resource($curl)) {
            curl_setopt_array($curl, $options);
            $response = (string) curl_exec($curl);
            curl_close($curl);
        }

        $response ??= null;

        (new LogService('DEBUG'))->log(__METHOD__, 'Request to Microsoft Teams', [
            'data' => $options,
            'response' => $response
        ]);

        return $response === '1';
    }
}
