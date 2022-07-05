<?php


namespace Qsoft\Personal;


use Bitrix\Main\Application;
use Bitrix\Main\Diag\SqlTrackerQuery;

class Debug
{
    protected static $instance = null;
    protected $debug;
    protected $queries = [];
    protected $time = null;
    protected $requests_time = null;
    protected $requests_count = 0;
    protected $query_time = null;
    protected $query_count = null;
    protected $request_start = null;

    public static function getInstance(): self
    {
        if (!self::$instance) {
            self::$instance = new static();
        }

        return self::$instance;
    }

    protected function __construct()
    {
        $this->debug = new \CDebugInfo(true);
    }

    public function start()
    {
        global $DB;
        $DB->ShowSqlStat = true;
        Application::getConnection()->startTracker(true);
        $this->debug->Start();
    }

    public function end()
    {
        $this->debug->Stop();

        $this->queries = [];

        /* @var SqlTrackerQuery $query */
        foreach ($this->debug->arResult['QUERIES'] as $query) {
            $this->queries[] = [
                'sql'  => str_replace(["\n", "\t"], '', $query->getSql()),
                'time' => $query->getTime(),
                'trace' => $query->getTrace(),
            ];
        }

        $this->time = $this->debug->arResult['TIME'];
        $this->query_time  = $this->debug->arResult['QUERY_TIME'];
        $this->query_count = (int)$this->debug->arResult['QUERY_COUNT'];
    }

    public function startRequest()
    {
        $this->request_start = time();
        $this->requests_count++;
    }

    public function endRequest()
    {
        $this->requests_time += time() - $this->request_start;
    }

    public function getTime()
    {
        return $this->time;
    }

    public function getQueryTime()
    {
        return $this->query_time;
    }

    public function getQueryCount(): int
    {
        return (int)$this->query_count;
    }

    public function getQueries(): array
    {
        return $this->queries;
    }

    public function toArray(): array
    {
        return [
            'time' => $this->time,
            'requests_time' => $this->requests_time,
            'requests_count' => $this->requests_count,
            'query_time' => $this->query_time,
            'query_count' => $this->query_count,
            'queries' => $this->queries
        ];
    }
}
