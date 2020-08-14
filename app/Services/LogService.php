<?php

namespace App\Services;

use App\Models\Log;
use RuntimeException;

/**
 * Class LogService
 * @package App\Services
 */
class LogService
{
    /**
     * @var array
     */
    private array $logInfo = [];

    /**
     * LogService constructor.
     * @param string $logType logging levels defined in the RFC 5424 specification:
     * emergency, alert, critical, error, warning, notice, info and debug.
     */
    public function __construct(string $logType = "INFO")
    {
        $this->logInfo['type'] = $logType;
    }

    /**
     * @return LogService
     */
    public static function instantiate(): LogService
    {
        return new self();
    }

    /**
     * @param string $logContext
     * @return $this
     */
    public function setContext(string $logContext): LogService
    {
        $this->logInfo['context'] = $logContext;
        return $this;
    }

    /**
     * @param string|null $logMessage
     * @return $this
     */
    public function setMessage(?string $logMessage = null): LogService
    {
        $this->logInfo['message'] = $logMessage;
        return $this;
    }

    /**
     * @param array|null $logExtra
     * @return $this
     */
    public function setExtra(?array $logExtra = null): LogService
    {
        if ($logExtra) {
            $this->logInfo['extra'] = json_encode($logExtra,JSON_HEX_APOS);
        }

        return $this;
    }

    /**
     * @return void
     * @throws RuntimeException if required 'context' field is not set
     */
    public function store()
    {
        if (!isset($this->logInfo['context'])) {
            throw new RuntimeException("Required 'context' field is not set");
        }

        Log::insert($this->logInfo);
    }

    /**
     * Shorten option to build log object.
     *
     * @param string|null $context
     * @param string|null $message
     * @param array|null $extra
     */
    public function log(string $context, string $message = null, array $extra = null)
    {
        $this->setContext($context)->setMessage($message)->setExtra($extra)->store();
    }
}
