<?php
namespace Qsoft\Personal\Repository;

use Bitrix\Main\Application;
use Bitrix\Main\ArgumentNullException;
use Bitrix\Main\ArgumentOutOfRangeException;
use Bitrix\Main\Type\DateTime;
use Exception;
use Qsoft\Personal\Admin\Options;

/**
 * Class PhotosRepository
 * @package Qsoft\Personal\Repository
 */
class PhotosRepository extends ListRepository
{
    protected function getUrl(): string
    {
        $options = Options::getInfo(
            "method_photos_path",
            "method_photos_type",
            "GET"
        );

        return $options['url'];
    }

    public function getById(int $id): ?string
    {
        $results = static::getList(['id' => $id], ['pageSize' => 1]);
        $result = current($results);

        if (empty($result)) {
           return null;
        }

        $photo = $result['photo'];

        if (empty($photo)) {
            return null;
        }

        return self::makeFile($photo, $result['filename']);
    }

    protected function makeFile(string $base64, ?string $fileName = null): ?string
    {
        $content = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $base64));

        if (empty($content)) {
            return null;
        }

        if (!$fileName) {
            $fileName = uniqid() . '.png';
        } else {
            $fileName = str_replace('jfif', 'jpg', $fileName);

            if (!\CFile::IsImage($fileName)) {
                $fileName = uniqid() . '.png';
            }
        }

        $tmp = Application::getDocumentRoot() . '/upload/tmp/' . $fileName;
        file_put_contents($tmp, $content);

        return $tmp;
    }
}
