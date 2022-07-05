<?php
namespace Qsoft\Personal\Repository;

use Bitrix\Main\ArgumentNullException;
use Bitrix\Main\ArgumentOutOfRangeException;
use Bitrix\Main\Type\Date;
use Bitrix\Main\Type\DateTime;
use Exception;

/**
 * Class ListRepository
 * @package Qsoft\Personal\Repository
 */
abstract class ListRepository extends Repository
{
    protected $pageCount = null;
    protected $totalCount = null;
    protected $offset = null;

    public function __construct()
    {
    }

    public function getPageCount()
    {
        return $this->pageCount;
    }

    public function getTotalCount()
    {
        return $this->totalCount;
    }

    /**
     * Метод для получения данных о пользователях
     *
     * @return array
     * @throws Exception
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     */
    public function getList(array $filter = [], array $nav = []): ?array
    {
        $params = self::prepareRequest();
        $params['parameters'] += self::prepareFilter($filter);
        $params['parameters'] += self::prepareNav($nav);

        $result = self::getResult($params);
        $body = $result['body'];

        if ($body['status'] !== 200) {
            throw new Exception('Ошибка получения данных');
        }

        if ($body['_meta']) {
            $this->pageCount = (int)$body['_meta']['pageCount'];
            $this->totalCount = (int)$body['_meta']['totalCount'];
        }

        return is_array($body['data']) ? $body['data'] : null;
    }

    /**
     * @inheritDoc
     *
     * @return array
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     */
    protected function prepareRequest(): array
    {
        $params = parent::prepareRequest();
        $params["isAssoc"] = true;
        $params['url'] .= $this->getUrl();
        $params['parameters'] = [];

        return $params;
    }

    protected function prepareFilter($filter)
    {
        foreach ($filter as &$val) {
            if (
                $val instanceof DateTime ||
                $val instanceof Date
            ) {
                $val = $val->getTimestamp();
            }
        }
        unset($val);

        return $filter;
    }

    protected function prepareNav($nav)
    {
        return [
            'page' => (int)$nav['pageNum'] ?? 0,
            'per-page' => $nav['pageSize'] ?? 50
        ];
    }

    abstract protected function getUrl(): string;
}
