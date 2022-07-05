<?php

namespace Qsoft\Committees\Kafka;

use Bitrix\Main\DB\SqlQueryException;
use Bitrix\Main\Localization\Loc;
use Qsoft\Committees\Admin\Options;
use Qsoft\Committees\Api\Events;
use Qsoft\Committees\Exception\CommitteesException;
use Qsoft\Committees\Exception\KafkaReloadException;

class Connection
{
    private const FULL_ADDRESS_TEMPLATE = '#address#:#port#';
    private const MINUTES_TO_TIMEOUT = 1;

    private string $address;
    private string $port;
    private string $topic;
    private int $timeoutTime;
    private \RdKafka\Conf $conf;
    private \Monolog\Logger $logger;

    public function __construct($logger)
    {
	    $this->setLogger($logger);

	    global $DB;

	    $DB->Disconnect();
	    $DB->DoConnect();

    	$requiredOptions = [
    		'kafka_address',
		    'kafka_port',
		    'kafka_topic'
	    ];

	    $options = Options::getOptionsWithoutCache($requiredOptions);

        $this->address = $options['kafka_address'];
        $this->port = $options['kafka_port'];
        $this->topic = $options['kafka_topic'];

        $this->init();
        $this->getMessages();
    }

    /**
     * Возвращает полный адрес для обращения к Kafka
     *
     * @return string
     */
    private function getFullAddress(): string
    {
        return str_replace(['#address#', '#port#'], [$this->address, $this->port], self::FULL_ADDRESS_TEMPLATE);
    }

    /**
     * Устанавливает значение свойства "логгер"
     *
     * @param $logger
     */
    private function setLogger($logger): void
    {
        $this->logger = $logger;
    }

    /**
     * Устанавливает значения свойств конфигурации подключения
     *
     */
    private function setConfigOptions(): void
    {
        $conf = new \RdKafka\Conf();

        $conf->set('client.id', 'kom-consumer-efko-vpool');
        $conf->set('group.id', 'kom-consumer');
        $conf->set('metadata.broker.list', $this->getFullAddress());
        $conf->set('security.protocol', 'sasl_plaintext');
        $conf->set('sasl.mechanism', 'PLAIN');
        $conf->set('sasl.username', 'bitrix');
        $conf->set('sasl.password', 'bitrix-KSNk4X');

        $this->conf = $conf;
    }

    /**
     * Инициализирует конфигурацию и время таймаута
     *
     */
    private function init(): void
    {
        $this->setConfigOptions();
        $this->setTimeoutTime(self::MINUTES_TO_TIMEOUT);
    }

    /**
     * Устанавливает время до таймаута в миллисекундах по переданному количеству минут
     * @param int $minutes
     */
    private function setTimeoutTime(int $minutes): void
    {
        $this->timeoutTime = $minutes * 60 * 1000;
    }

    /**
     * Возвращает время до таймаута в миллисекундах
     *
     * @return int
     */
    private function getTimeoutTime(): int
    {
        return $this->timeoutTime;
    }

    /**
     * Возвращает результат прослушивания
     *
     * @throws CommitteesException
     */
    private function getMessages(): void
    {
        $consumer = new \RdKafka\KafkaConsumer($this->conf);
        $consumer->subscribe([$this->topic]);
        $events = new Events($this->logger);

        while (true) {
            $message = $consumer->consume($this->getTimeoutTime());

            switch ($message->err) {

                case RD_KAFKA_RESP_ERR_NO_ERROR:
                    $offset = $message->offset;
                    $data = $message->payload;

                    $this->logger->info(Loc::getMessage(
                        'QSOFT_COMMITTEES_KAFKA_MESSAGE_RECEIVED',
                        [
                            '#MESSAGE_TEXT#' => $data,
                            '#MESSAGE_OFFSET#' => $offset
                        ]
                    ));

                    $events->uploadEventData(json_decode($data, true));

                    $this->logger->info(Loc::getMessage('QSOFT_COMMITTEES_KAFKA_MESSAGE_UPLOADED'));

                    break;

                case RD_KAFKA_RESP_ERR__TIMED_OUT:
	                throw new KafkaReloadException();
                    break;

                default:
                    $this->logger->error(Loc::getMessage(
                        'QSOFT_COMMITTEES_KAFKA_ERROR_CODE',
                        [
                            '#ERROR_MESSAGE#' => $message->errstr(),
                            '#ERROR_CODE#' => $message->err
                        ]
                    ));

                    throw new CommitteesException($message->errstr(), $message->err);
                    break;
            }
        }
    }
}
