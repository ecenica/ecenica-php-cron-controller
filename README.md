# Ecenica PHP Cron Controller

A simple, reliable, and configurable PHP cron controller developed by **Ecenica LTD**.  

**The Problem:** Traditional cron jobs require SSH access and crontab editing to change schedules. Need to disable a task? Change business hours? You have to edit crontab every time.

**The Solution:** Set up cron **once**, then control everything via a simple JSON file. No more crontab editing, perfect for non-technical users, shared hosting, or client self-service.

It gives you full control over when your PHP task scripts run — including days of the week, hours of the day, and a global enable/disable flag — all configurable via a single JSON file without ever touching cron again.

Ideal for lightweight automation tasks on shared hosting or VPS environments.

---

## 💡 Why Use This?

### The Traditional Cron Problem

When you set up a standard cron job:
```bash
*/5 * * * * /usr/bin/php /path/to/script.php
```

To change the schedule, you must:
- ❌ SSH into the server
- ❌ Edit crontab (`crontab -e`)
- ❌ Understand cron syntax
- ❌ Have server access permissions

### The Ecenica Solution

**Set up cron ONCE** (runs every minute, but only executes when conditions are met):
```bash
* * * * * /usr/bin/php /path/to/ControlledTask.php
```

**Control everything via JSON** - no server access needed:
```json
{
  "enabled": false,
  "start_hour": 9,
  "end_hour": 17,
  "days": ["Mon", "Tue", "Wed", "Thu", "Fri"]
}
```

### Perfect For:

✅ **Non-technical users** - Edit JSON via FTP/cPanel, no SSH needed  
✅ **Shared hosting** - Limited cron access, but file editing allowed  
✅ **Business hours only** - Run tasks 9-5 weekdays automatically  
✅ **Quick disable** - Emergency stop? Just set `"enabled": false`  
✅ **Client self-service** - Give clients control without server access  
✅ **Testing environments** - Disable tasks without code changes

---

## 🎯 Real-World Use Cases

| Scenario | Solution |
|----------|----------|
| **Emergency Stop** | Change `"enabled": false` - instant disable without cron access |
| **Business Hours** | Set `"start_hour": 9, "end_hour": 17` - only runs during work hours |
| **Weekdays Only** | Set `"days": ["Mon", "Tue", "Wed", "Thu", "Fri"]` |
| **Client Control** | Give client FTP access to edit config.json, not SSH/cron |
| **Seasonal Hours** | Adjust hours during holidays by editing JSON |
| **Dev vs Production** | Use different configs - disable in dev, enable in production |

---

## 🚀 Features

- 🟢 **Enable / Disable** easily via `config.json`
- 🕒 **Hour-based scheduling** control
- 📅 **Day-of-week** control
- 📜 **Automatic logging** with timestamps
- 🧩 **Self-contained** – no external dependencies
- ☁️ **Perfect for Ecenica Hosting** shared or managed servers

---

## ⚙️ Configuration

Create a `config.json` file in the same directory as the script:

```json
{
  "enabled": true,
  "start_hour": 9,
  "end_hour": 17,
  "days": ["Mon", "Tue", "Wed", "Thu", "Fri"]
}
```

### Configuration Options

| Option | Type | Description |
|--------|------|-------------|
| `enabled` | boolean | Master switch to enable/disable the task |
| `start_hour` | integer | Start hour (0-23) when task can run |
| `end_hour` | integer | End hour (0-23) when task can run |
| `days` | array | Days of week task can run (Mon, Tue, Wed, Thu, Fri, Sat, Sun) |

---

## 🖥️ Usage

### How It Works

```
┌─────────────────────────────────────────────────────────┐
│ Cron runs every minute (or every 5 minutes)            │
└─────────────────┬───────────────────────────────────────┘
                  │
                  ▼
┌─────────────────────────────────────────────────────────┐
│ Script checks config.json                               │
└─────────────────┬───────────────────────────────────────┘
                  │
                  ▼
         ┌────────┴────────┐
         │  Enabled?       │────── No ──► Stop
         └────────┬────────┘
                  │ Yes
                  ▼
         ┌────────┴────────┐
         │  Allowed day?   │────── No ──► Stop
         └────────┬────────┘
                  │ Yes
                  ▼
         ┌────────┴────────┐
         │  Allowed hour?  │────── No ──► Stop
         └────────┬────────┘
                  │ Yes
                  ▼
         ┌────────┴────────┐
         │  Run task!      │
         └─────────────────┘
```

### Step-by-Step Setup

**1. Upload Files**

Upload `ControlledTask.php` and create `config.json` on your server.

**2. Configure Your Task**

Edit the `run()` method in `ControlledTask.php` to add your custom logic.

**3. Test Manually**

Run the script manually for testing:

```bash
php ControlledTask.php
```

Check `task.log` to see if it ran or was blocked by config rules.

**4. Set Up Cron (One Time Only!)**

Add to crontab - this is the **only time** you'll need to edit cron:

```bash
# Run every minute (recommended - script handles its own scheduling)
* * * * * /usr/bin/php /path/to/ControlledTask.php

# OR run every 5 minutes (if you don't need minute-level precision)
*/5 * * * * /usr/bin/php /path/to/ControlledTask.php
```

**5. Control Via JSON**

From now on, control everything by editing `config.json` - **no more cron editing needed!**

---

## 🧰 Control

**Enable the task:**
```json
{ "enabled": true }
```

**Disable the task:**
```json
{ "enabled": false }
```

No need to modify cron – just edit the JSON config file!

---

## 🧾 Log Output

All activity is logged to `task.log` in the same directory:

```
[2025-11-12 14:30:15] Running main task...
[2025-11-12 14:35:20] Outside allowed hours
[2025-11-12 18:00:10] Not an allowed day
```

---

## 📋 Example Task Implementation

Edit the `run()` method to add your custom task logic:

```php
public function run(): void
{
    if (!$this->shouldRun()) {
        return;
    }
    
    $this->log("Running main task...");
    
    // Your task code here:
    // file_get_contents('https://api.example.com/endpoint');
    // $this->executeScript('/usr/bin/php', '/path/to/script.php');
    // Database operations, file processing, etc.
    
    $this->log("Task completed successfully");
}
```

---

## ⚠️ Support Notice

**This script is NOT covered under Ecenica LTD Technical Support or Managed Support agreements.**

This is provided as a free tool for your convenience. While we've tested it thoroughly, any modifications, customizations, or integration work required falls outside our standard support scope.

### Need Help?

- **Basic usage questions**: Refer to this README
- **Bug reports**: Submit via our GitHub repository
- **Custom development**: See below ⬇️

---

## 🛠️ Need Custom Development?

Ecenica LTD offers professional PHP development services for:

- ✨ Custom automation solutions
- 🔗 Third-party API integrations  
- 🎯 Bespoke business logic and workflows
- 🚀 Performance optimization
- 🔒 Security enhancements

**Contact us for a quote:** [https://www.ecenica.com](https://www.ecenica.com)

---

## 🧑‍💻 License

MIT License © 2025 Ecenica LTD

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

**THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.**

---

## 🌐 About Ecenica LTD

Ecenica LTD provides enterprise-grade hosting and development solutions.

**Visit us:** [https://www.ecenica.com](https://www.ecenica.com)
