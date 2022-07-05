<?php


namespace Qsoft\Committees\Api;


use Qsoft\Committees\Admin\Options;
use Bitrix\Main\Web\Uri;

class Request
{
    private string $methodUri;
    private string $xApiKey;
    private array $headers = [];
    private Events $events;

    public Response $response;

    public function __construct(Events $events)
    {
        $this->events = $events;
        $this->xApiKey = Options::getOption('x_api_key');

        $this->setMethodUri();
        $this->events->getLogger()->info($this->getMethodUri());
        $this->initHeaders();
        $this->initQuery();
    }

    /**
     * Инициализирует запрос на получение данных
     *
     */
    private function initQuery(): void
    {
        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, $this->getMethodUri());
        curl_setopt($curl, CURLOPT_HTTPHEADER, $this->getHeaders());
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $response = new Response();
        $apiResponse = curl_exec($curl);

        if ($apiResponse === false) {
            $response->setStatus(ResponseStatus::STATUS_ERROR);
        } else {
            $response->setResponseBody(json_decode($apiResponse, true));
            $response->setStatus(ResponseStatus::STATUS_SUCCESS);
        }

        curl_close($curl);

        $this->setResponse($response);
    }

    /**
     * Возвращает объект событий
     *
     * @return Events
     */
    private function getEvents(): Events
    {
        return $this->events;
    }

    /**
     * Возвращает указанные get-параметры запроса
     *
     * @return array
     */
    private function getParams(): array
    {
        $params = [];

        if (($pageNum = $this->getEvents()->getPageNum()) > 0) {
            $params['page'] = $pageNum;
        }

        if (($perPage = $this->getEvents()->getPerPage()) > 0) {
            $params['per-page'] = $perPage;
        }

        if (($beginDateTimestamp = $this->getEvents()->getBeginDateTimestamp()) > 0) {
            $params['dtEventFrom'] = $beginDateTimestamp;
        }

        if (($endDateTimestamp = $this->getEvents()->getEndDateTimestamp()) > 0) {
            $params['dtEventTo'] = $endDateTimestamp;
        }

        return $params;
    }

    /**
     * Устанавливает значение свойства "строка запроса"
     *
     * @throws \Bitrix\Main\ArgumentNullException
     * @throws \Bitrix\Main\ArgumentOutOfRangeException
     */
    private function setMethodUri(): void
    {
        $baseUri = Options::getOption('api_method_uri');
        $uri = new Uri($baseUri);
        $params = $this->getParams();
        $uri->addParams($params);

        $this->methodUri = $uri->getUri();
    }

    /**
     * Возвращает строку запроса
     *
     * @return string
     */
    private function getMethodUri(): string
    {
        return $this->methodUri;
    }

    /**
     * Возвращает объект ответа
     *
     * @return Response
     */
    public function getResponse(): Response
    {
        return $this->response;
    }

    /**
     * Устанавливает значение свойства "ответ"
     *
     * @param Response $response
     */
    private function setResponse(Response $response): void
    {
        $this->response = $response;
    }

    /**
     * Инициализирует заголовки запроса
     *
     */
    private function initHeaders(): void
    {
        $this->addHeader('X-API-Key', $this->xApiKey);
    }

    /**
     * Возвращает заголовки запроса
     *
     * @return array
     */
    private function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * Добавляет заголовок в список заголовков
     *
     * @param string $name
     * @param string $value
     */
    private function addHeader(string $name, string $value): void
    {
        $this->headers[] = $name . ': ' . $value;
    }
}
