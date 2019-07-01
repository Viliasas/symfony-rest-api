<?php

namespace App\Model;

use Symfony\Component\Serializer\Annotation\Groups;

class ApiResponse
{
    /**
     * @var bool Response status
     * @Groups("api")
     */
    private $success;

    /**
     * @var string Response text
     * @Groups("api")
     */
    private $message;

    /**
     * @var mixed Response data
     * @Groups("api")
     */
    private $data;

    /**
     * ApiResponse constructor.
     *
     * @param string $message Response text
     * @param bool $success Response status
     * @param mixed $data Response data
     */
    public function __construct($message, $success = false, $data = null)
    {
        $this->message = $message;
        $this->success = $success;
        $this->data = $data;
    }

    /**
     * @return bool
     */
    public function isSuccess(): bool
    {
        return $this->success;
    }

    /**
     * @param bool $success
     */
    public function setSuccess(bool $success): void
    {
        $this->success = $success;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @param string $message
     */
    public function setMessage(string $message): void
    {
        $this->message = $message;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param mixed $data
     */
    public function setData($data): void
    {
        $this->data = $data;
    }
}
