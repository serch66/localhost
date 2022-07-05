<?php


namespace Qsoft\Committees\Api;


class Response
{
    private $responseBody;
    private string $status;

    /**
     * Устанавливает значение свойства "статус"
     *
     * @param $status
     */
    public function setStatus($status): void
    {
        $this->status = $status;
    }

    /**
     * Возвращает статус
     *
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * Устанавливает значение свойства "тело ответа"
     *
     * @param $response
     */
    public function setResponseBody($response): void
    {
        $this->responseBody = $response;
    }

    /**
     * Возвращает тело ответа
     *
     * @return array
     */
    public function getResponseBody(): array
    {
        return $this->responseBody;
    }
}
