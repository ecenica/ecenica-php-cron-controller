<?php
class ControlledTask
{
    private string $configFile;
    private string $logFile;
    private array $config;

    public function __construct(string $configFile, string $logFile)
    {
        $this->configFile = $configFile;
        $this->logFile    = $logFile;
        $this->loadConfig();
    }

    private function loadConfig(): void
    {
        if (!file_exists($this->configFile)) {
            $this->log("Missing config file: {$this->configFile}");
            exit;
        }

        $json = file_get_contents($this->configFile);
        $this->config = json_decode($json, true);

        if (!$this->config || !isset($this->config['enabled'])) {
            $this->log("Invalid configuration format");
            exit;
        }
    }

    private function log(string $message): void
    {
        file_put_contents($this->logFile, date('[Y-m-d H:i:s] ') . $message . PHP_EOL, FILE_APPEND);
    }

    private function isEnabled(): bool
    {
        return (bool)$this->config['enabled'];
    }

    private function isWithinAllowedHours(): bool
    {
        $currentHour = (int)date('G');
        $start = $this->config['start_hour'] ?? 0;
        $end   = $this->config['end_hour'] ?? 23;
        return ($currentHour >= $start && $currentHour <= $end);
    }

    private function isAllowedDay(): bool
    {
        $currentDay = date('D'); // e.g., Mon, Tue, Wed
        $allowedDays = $this->config['days'] ?? ['Mon', 'Tue', 'Wed', 'Thu', 'Fri'];
        return in_array($currentDay, $allowedDays);
    }

    private function shouldRun(): bool
    {
        if (!$this->isEnabled()) {
            $this->log("Task disabled via config");
            return false;
        }
        if (!$this->isAllowedDay()) {
            $this->log("Not an allowed day");
            return false;
        }
        if (!$this->isWithinAllowedHours()) {
            $this->log("Outside allowed hours");
            return false;
        }
        return true;
    }

    public function run(): void
    {
        if (!$this->shouldRun()) {
            return;
        }

        $this->log("Running main task...");
        // Example task:
        // file_get_contents('https://example.com/task');
        // shell_exec('/usr/bin/php /path/to/another_script.php');
    }
}

$task = new ControlledTask(__DIR__ . '/config.json', __DIR__ . '/task.log');
$task->run();
