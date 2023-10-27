<?php

namespace App\Exceptions;

class Error
{
    private int $code;
    private string $message;
    private array $data;

    public function __construct(int $code, string $message, array $data = [])
    {
        $this->code = $code;
        $this->message = $message;
        $this->data = $data;
    }

    public function setData(string $key, array $data): void
    {
        $this->data[$key] = $data;
    }

    public function toArray(): array
    {
        $error = [
            'error' => [
                'code'    => $this->code,
                'message' => $this->message,
            ],
        ];

        if ($this->data) {
            $error['error']['data'] = $this->data;
        }

        return $error;
    }

    /**
     * @param int $code
     */
    public function setCode(int $code): void
    {
        $this->code = $code;
    }

    /**
     * @param string $message
     */
    public function setMessage(string $message): void
    {
        $this->message = $message;
    }
}
