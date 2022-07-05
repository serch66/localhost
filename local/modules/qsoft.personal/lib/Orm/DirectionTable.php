<?php

namespace Qsoft\Personal\Orm;

use Bitrix\Iblock\SectionTable;
use Bitrix\Main\Loader;
use Qsoft\Personal\Exception\Exception;

if (!\Bitrix\Main\Loader::includeModule('iblock')) {
    throw new Exception('module iblock is not installed');
}

class DirectionTable extends \Bitrix\Iblock\SectionTable
{
    public static function getUfId()
    {
        return 'CRM_DEAL';
    }

    public static function getIblockId()
    {
        return 'CRM_DEAL';
    }
}
