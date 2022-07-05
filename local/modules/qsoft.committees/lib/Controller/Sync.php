<?php


namespace Qsoft\Committees\Controller;


use Bitrix\Main\Config\Option;
use Bitrix\Main\Engine\Controller;
use Bitrix\Main\Localization\Loc;
use Qsoft\Committees\Admin\Options;
use Bitrix\Main\Loader;
use Qsoft\Committees\Logger\Logger;
use Qsoft\Committees\Api\Events;

class Sync extends Controller
{

    private int $pageNumber;
    private \Monolog\Logger $logger;
    private const IS_RUNNING = 'Y';
    private const NOT_RUNNING = 'N';

    /**
     * @return array
     */
    public function configureActions()
    {
        return [
            'start' => [
                'prefilters' => []
            ],
            'interrupt' => [
                'prefilters' => []
            ],
            'errorFinish' => [
                'prefilters' => []
            ],
            'successFinish' => [
                'prefilters' => []
            ]
        ];
    }

    /**
     * Метод выгрузки данных страницы событий из МС в Битрикс
     * Инициализируется по аяксу
     *
     * @return array
     * @throws \Bitrix\Main\LoaderException
     */
    public function startAction(): array
    {
        Loader::includeModule('qsoft.committees');
        Loader::includeModule('highloadblock');

        $this->logger = Logger::get('full_upload');

        try {
            $this->initUploading();

            $this->pageNumber = (int)Options::getOption('page_number');
            $events = new Events($this->logger);
            $events->uploadPage();

            $meta = $events->getMeta();

            if ((int)$meta['pageCount'] !== $this->pageNumber) {
                $this->incrementPageNumberOption();
            } else {
                $this->setUploadingStatus(self::NOT_RUNNING);
                $this->setOptionFirstPage();
            }

            return ['status' => 'success', 'meta' => $meta, 'pageNumber' => $this->pageNumber];
        } catch (\Throwable $e) {
            $this->setUploadingStatus(self::NOT_RUNNING);

            $this->logger->critical(Loc::getMessage('QSOFT_COMMITTEES_LOG_UPLOAD_ERROR_LOG_ERROR', ['#ERROR#' => $e]));
            $this->logger->critical(Loc::getMessage('QSOFT_COMMITTEES_LOG_UPLOAD_ERROR_LOG_ERROR_CODE',  ['#ERROR_CODE#' => $e->getCode()]));
            $this->logger->critical(Loc::getMessage('QSOFT_COMMITTEES_LOG_UPLOAD_ERROR_LOG_LINE',  ['#LINE#' => $e->getLine()]));
            $this->logger->critical(Loc::getMessage('QSOFT_COMMITTEES_LOG_UPLOAD_ERROR_LOG_FILENAME',  ['#FILE_NAME#' => $e->getFile()]));
            $this->logger->critical(Loc::getMessage('QSOFT_COMMITTEES_LOG_UPLOAD_ERROR_LOG_TRACE',  ['#TRACE#' => json_encode($e->getTrace(), JSON_UNESCAPED_UNICODE)]));
            $this->logger->critical(Loc::getMessage('QSOFT_COMMITTEES_LOG_UPLOAD_ERROR_LOG_MESSAGE', ['#MESSAGE#' => $e->getMessage()]));

            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public function interruptAction(): array
    {
        return $this->finishAjaxSync(Loc::getMessage(
            'QSOFT_COMMITTEES_LOG_UPLOAD_INTERRUPTED',
            ['#SEPARATOR#' => Loc::getMessage('QSOFT_COMMITTEES_LOG_UPLOAD_ENDED_SEPARATOR')]
        ));
    }

    public function errorFinishAction(): array
    {
        return $this->finishAjaxSync(Loc::getMessage(
            'QSOFT_COMMITTEES_LOG_UPLOAD_ENDED_WITH_ERROR',
            ['#SEPARATOR#' => Loc::getMessage('QSOFT_COMMITTEES_LOG_UPLOAD_ENDED_SEPARATOR')]
        ));
    }

    public function successFinishAction(): array
    {
        return $this->finishAjaxSync(Loc::getMessage(
            'QSOFT_COMMITTEES_LOG_UPLOAD_ENDED_SUCCESSFULLY',
            [
                '#SEPARATOR#' => Loc::getMessage('QSOFT_COMMITTEES_LOG_UPLOAD_ENDED_SEPARATOR')
            ]
        ));
    }

    private function setUploadingStatus(string $status): void
    {
        Option::set(\Qsoft\Committees\MODULE_ID, 'is_uploading', $status);
    }

    public static function checkIfUploading(): bool
    {
        return Options::getOption('is_uploading') === self::IS_RUNNING;
    }

    private function initUploading(): void
    {
        $isUploading = self::checkIfUploading();

        if (!$isUploading) {
            $this->setUploadingStatus(self::IS_RUNNING);

            $this->logger->info(Loc::getMessage(
                'QSOFT_COMMITTEES_LOG_UPLOAD_STARTED',
                ['#SEPARATOR#' => Loc::getMessage('QSOFT_COMMITTEES_LOG_UPLOAD_ENDED_SEPARATOR')]
            ));
        }
    }

    private function finishAjaxSync($message): array
    {
        $this->logger = Logger::get('full_upload');
        $this->logger->info($message);

        $this->setUploadingStatus(self::NOT_RUNNING);

        return ['status' => 'success'];
    }

    /**
     * Увеличивает значение настройки "Страница начала выгрузки" модуля на 1
     *
     * @throws \Bitrix\Main\ArgumentOutOfRangeException
     */
    private function incrementPageNumberOption(): void
    {
        Option::set(\Qsoft\Committees\MODULE_ID, 'page_number', $this->pageNumber + 1);
    }

    /**
     * Устанавливает значение 1 настройке "Страница начала выгрузки" модуля
     * @throws \Bitrix\Main\ArgumentOutOfRangeException
     */
    private function setOptionFirstPage(): void
    {
        Option::set(\Qsoft\Committees\MODULE_ID, 'page_number', 1);
    }

}
