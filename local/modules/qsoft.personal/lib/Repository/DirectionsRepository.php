<?php
namespace Qsoft\Personal\Repository;

use Bitrix\Iblock\Model\Section;
use Bitrix\Iblock\SectionTable;
use Bitrix\Main\ArgumentNullException;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Loader;
use Qsoft\Personal\Admin\Options;
use Qsoft\Personal\Exception\Exception;

/**
 * Class DirectionsRepository
 * @package Qsoft\Personal\Repository
 */
class DirectionsRepository extends ListRepository
{
    public function __construct()
    {
        Loader::includeModule('iblock');
        parent::__construct();
    }

    protected function getUrl(): string
    {
        $options = Options::getInfo(
            "method_directions_path",
            "method_directions_type",
            "GET"
        );

        return $options['url'];
    }

    public static function add(array $fields): int
    {
        if (!self::getIblockId()) {
            return false;
        }

        $section = new \CIBlockSection();
        $fields = self::prepareFields(null, $fields);

        if (!$fields['IBLOCK_SECTION_ID']) {
            $rootSection = self::getRootSection();
            if ($rootSection) {
                $preparedFields['IBLOCK_SECTION_ID'] = $rootSection;
            }
        }

        $id = (int)$section->Add($fields);

        if (!$id) {
            throw new Exception($section->LAST_ERROR);
        }

        return $id;
    }

    public static function update(int $id, array $fields): bool
    {
        if (!self::getIblockId()) {
            return false;
        }

        if (!self::getIblockId()) {
            return false;
        }

        $section = new \CIBlockSection();
        $fields = self::prepareFields($id, $fields);

        if (!$section->Update($id, $fields)) {
            throw new Exception($section->LAST_ERROR);
        }

        return true;
    }

    protected static function prepareFields(?int $id, array $fields): array
    {
        if (!$fields['id']) {
            throw new ArgumentNullException('id');
        }

        $xml_id = (int)$fields['id'];

        if ($xml_id === Options::getDefaultDirectionId()) {
            $name = Options::getDefaultDirectionName();
        } else {
            $name = trim($fields['name']);
        }

        $preparedFields = [
            'ACTIVE' => 'Y',
            'IBLOCK_ID' => self::getIblockId(),
            'NAME' => $name,
            'XML_ID' => $xml_id,
        ];

        if (empty($fields['parent']) || $fields['parent'] === $id) {
            $preparedFields['IBLOCK_SECTION_ID'] = self::getRootSection();
        } else {
            $preparedFields['IBLOCK_SECTION_ID'] = $fields['parent'];
        }

        if ($preparedFields['IBLOCK_SECTION_ID'] === $id) {
            unset($preparedFields['IBLOCK_SECTION_ID']);
        }

        return $preparedFields;
    }

    public static function getIblockId(): int
    {
        return (int)Option::get('intranet', 'iblock_structure', 0);
    }

    protected static function getRootSection(): int
    {
        static $id = 0;

        if (!$id) {
            $rs = \CIBlockSection::GetList(
                [],
                ['IBLOCK_ID' => self::getIblockId(), 'IBLOCK_SECTION_ID' => false],
                false,
                ['ID'],
                ['nTopCount' => 1]
            );

            $ar = $rs->Fetch();
            $id = (int)$ar['ID'];
        }

        return $id;
    }

    public static function getEntity(): SectionTable
    {
        $class = Section::compileEntityByIblock(static::getIblockId());
        return new $class;
    }

    public static function resort(): bool
    {
        \CIBlockSection::ReSort(DirectionsRepository::getIblockId());
        return true;
    }

    public static function getAllSections(): array
    {
        static $sections = null;

        if (!is_array($sections)) {
            $sections = DirectionsRepository::getEntity()->getList(
                [
                   'filter' => [
                       'IBLOCK_ID' => DirectionsRepository::getIblockId(),
                       '!XML_ID' => false
                   ],
                   'select' => ['ID', 'XML_ID', 'UF_HEAD', 'NAME', 'IBLOCK_SECTION_ID'],
                   'cache' => ['ttl' => 3600]
                ]
            )->fetchAll();
            $sections = array_column($sections, null, 'XML_ID');
        }

        return $sections;
    }
}
