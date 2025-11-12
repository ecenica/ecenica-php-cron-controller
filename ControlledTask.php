<?php
/**
 * Controlled Task Scheduler
 * 
 * A flexible task execution system that adds user-friendly scheduling controls on top of cron.
 * 
 * WHY USE THIS?
 * Traditional cron jobs require SSH access and crontab editing to change schedules. This script
 * lets you set up cron ONCE, then control scheduling via a simple JSON file - perfect for:
 * - Non-technical users who need to enable/disable tasks
 * - Shared hosting environments with limited cron access
 * - Business-hours-only execution (9-5, weekdays only, etc.)
 * - Quick emergency disable without touching crontab
 * - Client self-service without giving SSH access
 * 
 * HOW IT WORKS:
 * 1. Set up cron to run this script every minute: * * * * * /usr/bin/php script.php
 * 2. The script checks config.json for scheduling rules
 * 3. Task only executes when ALL conditions are met (enabled, correct day, correct hour)
 * 4. Change schedule anytime by editing JSON - no cron changes needed!
 * 
 * @author Ecenica LTD
 * @license MIT License
 * @version 1.0.0
 * @link https://www.ecenica.com
 * 
 * MIT License
 * 
 * Copyright (c) 2025 Ecenica LTD
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 * 
 * DISCLAIMER:
 * This software is provided as-is without any guarantees or warranty. Use at your own risk.
 * The authors and Ecenica LTD are not responsible for any damage, data loss, or other
 * issues that may arise from the use of this software. Always test thoroughly in a
 * development environment before deploying to production.
 * 
 * SUPPORT NOTICE:
 * This script is NOT covered under Ecenica LTD Technical Support or Managed Support agreements.
 * It is provided as a free tool for your convenience. While we've tested it thoroughly,
 * any modifications, customizations, or integration work required falls outside our
 * standard support scope.
 * 
 * NEED CUSTOM DEVELOPMENT?
 * Ecenica LTD offers professional PHP development services for custom automation,
 * integrations, and bespoke solutions tailored to your business needs.
 * Contact us for a quote: https://www.ecenica.com
 */

class ControlledTask
{
    /**
     * Path to the configuration file
     * @var string
     */
    private string $configFile;
    
    /**
     * Path to the log file
     * @var string
     */
    private string $logFile;
    
    /**
     * Configuration array loaded from JSON
     * @var array
     */
    private array $config;
    
    /**
     * Constructor - Initialize the task with config and log file paths
     * 
     * @param string $configFile Path to config.json
     * @param string $logFile Path to task.log
     */
    public function __construct(string $configFile, string $logFile)
    {
        $this->configFile = $configFile;
        $this->logFile    = $logFile;
        $this->loadConfig();
    }
    
    /**
     * Load and validate configuration from JSON file
     * 
     * @return void
     */
    private function loadConfig(): void
    {
        if (!file_exists($this->configFile)) {
            $this->log("Missing config file: {$this->configFile}");
            exit(1);
        }
        
        $json = file_get_contents($this->configFile);
        
        if ($json === false) {
            $this->log("Unable to read config file: {$this->configFile}");
            exit(1);
        }
        
        $this->config = json_decode($json, true);
        
        if (!$this->config || !isset($this->config['enabled'])) {
            $this->log("Invalid configuration format - 'enabled' key is required");
            exit(1);
        }
    }
    
    /**
     * Write a message to the log file with timestamp
     * 
     * @param string $message The message to log
     * @return void
     */
    private function log(string $message): void
    {
        $timestamp = date('[Y-m-d H:i:s]');
        $logEntry = "{$timestamp} {$message}" . PHP_EOL;
        file_put_contents($this->logFile, $logEntry, FILE_APPEND | LOCK_EX);
    }
    
    /**
     * Check if the task is enabled in configuration
     * 
     * @return bool True if enabled, false otherwise
     */
    private function isEnabled(): bool
    {
        return (bool)$this->config['enabled'];
    }
    
    /**
     * Check if current hour is within allowed time window
     * 
     * @return bool True if within allowed hours, false otherwise
     */
    private function isWithinAllowedHours(): bool
    {
        $currentHour = (int)date('G');
        $start = $this->config['start_hour'] ?? 0;
        $end   = $this->config['end_hour'] ?? 23;
        
        return ($currentHour >= $start && $currentHour <= $end);
    }
    
    /**
     * Check if current day is in the allowed days list
     * 
     * @return bool True if allowed day, false otherwise
     */
    private function isAllowedDay(): bool
    {
        $currentDay = date('D'); // Mon, Tue, Wed, Thu, Fri, Sat, Sun
        $allowedDays = $this->config['days'] ?? ['Mon', 'Tue', 'Wed', 'Thu', 'Fri'];
        
        return in_array($currentDay, $allowedDays, true);
    }
    
    /**
     * Determine if the task should run based on all conditions
     * 
     * @return bool True if all conditions are met, false otherwise
     */
    private function shouldRun(): bool
    {
        if (!$this->isEnabled()) {
            $this->log("Task disabled via config");
            return false;
        }
        
        if (!$this->isAllowedDay()) {
            $this->log("Not an allowed day: " . date('D'));
            return false;
        }
        
        if (!$this->isWithinAllowedHours()) {
            $this->log("Outside allowed hours: " . date('G:i'));
            return false;
        }
        
        return true;
    }
    
    /**
     * Execute the main task if all conditions are met
     * 
     * @return void
     */
    public function run(): void
    {
        if (!$this->shouldRun()) {
            return;
        }
        
        $this->log("Running main task...");
        
        try {
            // ============================================
            // YOUR CUSTOM TASK CODE GOES HERE
            // ============================================
            
            // Example 1: Make an API call
            // $response = file_get_contents('https://example.com/api/task');
            // $this->log("API Response: " . $response);
            
            // Example 2: Execute another PHP script
            // $output = shell_exec('/usr/bin/php /path/to/another_script.php');
            // $this->log("Script output: " . $output);
            
            // Example 3: Database operations
            // $pdo = new PDO('mysql:host=localhost;dbname=mydb', 'user', 'pass');
            // $stmt = $pdo->query('SELECT COUNT(*) FROM tasks');
            // $this->log("Task count: " . $stmt->fetchColumn());
            
            // Example 4: File processing
            // $files = glob('/path/to/files/*.txt');
            // foreach ($files as $file) {
            //     // Process each file
            // }
            
            // ============================================
            // END OF CUSTOM TASK CODE
            // ============================================
            
            $this->log("Task completed successfully");
            
        } catch (Exception $e) {
            $this->log("ERROR: Task failed - " . $e->getMessage());
        }
    }
}

// ============================================
// SCRIPT EXECUTION
// ============================================

try {
    $task = new ControlledTask(
        __DIR__ . '/config.json',
        __DIR__ . '/task.log'
    );
    $task->run();
} catch (Exception $e) {
    error_log("ControlledTask Fatal Error: " . $e->getMessage());
    exit(1);
}
