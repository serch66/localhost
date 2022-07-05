<?php

namespace Fbit\Forms;

use Bitrix\Crm\DealTable;
use Bitrix\Main\Loader;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Page\Asset;
use Bitrix\UI\Buttons\Color;
use Bitrix\UI\Buttons\Split\Button;

class EventHandler
{
    function onProlog ()
    {
        [
            $entityType,
            $entityId,
        ] = self::getEntityInfo();

        if (!$entityType || !$entityId) {
            return;
        }

        self::add1cButtonToPageTitle($entityType, $entityId);
    }

    static function getEntityInfo ()
    {
        global $APPLICATION;
        preg_match(
            '/crm\/([A-z]+)\/details\/(\d+)/',
            $APPLICATION->getCurPage(),
            $match
        );

        return [
            $match[1],
            $match[2],
        ];
    }

    static function add1cButtonToPageTitle ($entityType, $entityId)
    {
        $docType  = self::getDocType($entityType, $entityId);
        $BaseForm = new ConfigForms($docType, $entityId);
        $button   = $BaseForm->GetMenu();

        if (!$button['ITEMS']) {
            return;
        }

        foreach ($button['ITEMS'] as &$item) {
            $item['onclick'] = ['code' => $item['onclick']];
        }
        unset($item);

        $render = Button::create([
            'text'      => $button['TEXT'],
            'color'     => Color::LIGHT_BORDER,
            'menu'      => ['items' => $button['ITEMS']],
            'className' => 'force-order-99'
        ])->render();
        // because 3rd arg in AddViewContent not working
        Asset::getInstance()->addString(
            '<style> .force-order-99 { order: 99 } </style>'
        );

        global $APPLICATION;

        $APPLICATION->AddViewContent('inside_pagetitle', $render);
    }

    static function getDocType ($entityType, $entityId)
    {
        $entityType = ucfirst($entityType);
        if ($entityType !== 'Deal') {
            return $entityType;
        }

        Loader::includeModule('crm');
        $categoryId = DealTable::query()
            ->addSelect('CATEGORY_ID')
            ->addFilter('ID', $entityId)
            ->fetch()['CATEGORY_ID'];

        return $entityType . '_' . $categoryId;
    }
}
