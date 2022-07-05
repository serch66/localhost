<?php
namespace Qsoft\Personal\Repository;

use Qsoft\Personal\Debug;
use RuntimeException;
use Bitrix\Main\ArgumentNullException;
use Bitrix\Main\ArgumentOutOfRangeException;
use Qsoft\Personal\Admin\Options;
use Qsoft\Personal\Exception\Exception;
use Unirest\Method;
use Unirest\Request;
use Unirest\Response;

/**
 * Class Repository
 *
 * @package Qsoft\Personal\Repository
 */
abstract class Repository
{
    /**
     * Метод для подготовки параметров для запроса
     *
     * @return array
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     */
    protected function prepareRequest(): array
    {
        return [
            'url' => Options::getUrl(),
            'headers' => [
                "Accept" => "application/json",
                "Content-Type" => "application/json",
                "X-API-Key" => Options::getApiKey()
            ],
            'parameters' => null,
        ];
    }

    /**
     * Метод для выполнения запроса
     *
     * @param $type
     * @param string $url
     * @param array $headers
     * @param string|null $body
     * @param string|null $username
     * @param string|null $password
     * @param bool|null $isAssoc
     * @return Response
     */
    protected static function runRequest(
        string $method,
        string $url,
        array $headers = array(),
        $body = null,
        ?bool $isAssoc = false
    ): Response
    {
        Debug::getInstance()->startRequest();

        if ($isAssoc) {
            Request::jsonOpts(true);
        }

        switch ($method) {
            case Method::GET:
                $response = Request::get(
                    $url,
                    $headers,
                    $body
                );
                break;
            case Method::POST:
                $response =  Request::post(
                    $url,
                    $headers,
                    $body
                );
                break;
            case Method::DELETE:
                $response =  Request::delete(
                    $url,
                    $headers,
                    $body
                );
                break;
            case Method::PUT:
                $response =  Request::put(
                    $url,
                    $headers,
                    $body
                );
                break;
            default:
                Debug::getInstance()->endRequest();
                throw new RuntimeException('Unexpected value');
        }

        Debug::getInstance()->endRequest();

        return $response;
    }

    /**
     * Метод для отправки запроса и обработки ответа
     *
     * @param array $options
     * @param array $params
     * @return array
     * @throws Exception
     */
    protected static function getResult(array $params): array
    {
        $response = self::runRequest(
            $params['method'] ?? Method::GET,
            $params['url'],
            $params['headers'],
            $params['parameters'],
            $params["isAssoc"] === true
        );

        if ($response->code !== 200) {
            throw new Exception($response->body['message']);
        }

        return [
            'code' => $response->code,        // HTTP Status code
            'headers' => $response->headers,     // Headers
            'body' => $response->body,        // Parsed body
            'rawBody' => $response->raw_body     // Unparsed body
        ];
    }
}
