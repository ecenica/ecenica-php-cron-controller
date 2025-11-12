# Ecenica PHP Cron Controller

A simple, reliable, and configurable PHP cron controller developed by **Ecenica Hosting**.  
It gives you full control over when your PHP task scripts run — including days of the week, hours of the day, and a global enable/disable flag — all configurable via a single JSON file.

Ideal for lightweight automation tasks on shared hosting or VPS environments.

## 🚀 Features
- 🟢 Enable / Disable easily via `config.json`
- 🕒 Hour-based scheduling control
- 📅 Day-of-week control
- 📜 Automatic logging
- 🧩 Self-contained – no external dependencies
- ☁️ Perfect for Ecenica Hosting shared or managed servers

## ⚙️ Configuration
```json
{
  "enabled": true,
  "start_hour": 9,
  "end_hour": 17,
  "days": ["Mon", "Tue", "Wed", "Thu", "Fri"]
}
```

## 🖥️ Usage
1. Run manually:
```bash
php ControlledTask.php
```
2. Set up a cron job:
```bash
* * * * * /usr/bin/php /path/to/ControlledTask.php
```

## 🧰 Control
Enable/Disable task easily by editing `config.json`.

## 🧾 Log Output
Logs will be written to `task.log` with timestamps.

## 🧑‍💻 License
MIT License © 2025 Ecenica Hosting
