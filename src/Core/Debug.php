<?php
namespace LorPHP\Core;

class Debug {
    private static $instance = null;
    private $logs = [];
    private $startTime;
    private $queries = [];
    private $memoryPeaks = [];

    public function __construct() {
        $this->startTime = microtime(true);
        self::$instance = $this;
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function log($message, $type = 'info') {
        $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)[0];
        $this->logs[] = [
            'timestamp' => microtime(true),
            'type' => $type,
            'message' => $message,
            'file' => $backtrace['file'],
            'line' => $backtrace['line'],
            'memory' => memory_get_usage(true)
        ];

        if ($type === 'error') {
            $this->logToFile($message, $backtrace);
        }
    }

    public function logQuery($query, $params = [], $executionTime = null) {
        $this->queries[] = [
            'query' => $query,
            'params' => $params,
            'execution_time' => $executionTime,
            'timestamp' => microtime(true)
        ];
    }

    private function logToFile($message, $backtrace) {
        $logFile = __DIR__ . '/../../storage/logs/error-' . date('Y-m-d') . '.log';
        $logMessage = sprintf(
            "[%s] %s in %s:%d\n",
            date('Y-m-d H:i:s'),
            $message,
            $backtrace['file'],
            $backtrace['line']
        );
        file_put_contents($logFile, $logMessage, FILE_APPEND);
    }

    public function getExecutionTime() {
        return microtime(true) - $this->startTime;
    }

    public function getMemoryUsage() {
        return [
            'current' => memory_get_usage(true),
            'peak' => memory_get_peak_usage(true)
        ];
    }

    public function renderDebugBar() {
        if (!($this->config['app']['debug'] ?? false)) {
            return '';
        }

        $executionTime = round($this->getExecutionTime() * 1000, 2);
        $memoryUsage = round(memory_get_usage(true) / 1024 / 1024, 2);
        $queryCount = count($this->queries);
        $logCount = count($this->logs);

        return <<<HTML
        <div class="fixed bottom-0 left-0 right-0 bg-gray-800 text-white p-2 text-sm">
            <div class="container mx-auto flex justify-between items-center">
                <div>Execution: {$executionTime}ms</div>
                <div>Memory: {$memoryUsage}MB</div>
                <div>Queries: {$queryCount}</div>
                <div>Logs: {$logCount}</div>
                <button onclick="toggleDebugPanel()" class="bg-blue-500 px-2 py-1 rounded">Details</button>
            </div>
        </div>
        HTML;
    }
}
