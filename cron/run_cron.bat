@echo off
:: BookEase - Exchange Rate Updater
:: Run this via Windows Task Scheduler every hour.
::
:: Setup (run once in an elevated Command Prompt):
::   schtasks /create /tn "BookEase_ExchangeRates" /tr "\"C:\xampp\htdocs\BookEase\cron\run_cron.bat\"" /sc hourly /mo 1 /st 00:00 /f
::
:: To delete the task:
::   schtasks /delete /tn "BookEase_ExchangeRates" /f

set PHP_EXE=C:\xampp\php\php.exe
set SCRIPT=C:\xampp\htdocs\BookEase\cron\update_exchange_rates.php
set LOG=C:\xampp\htdocs\BookEase\cron\cron.log

echo [%DATE% %TIME%] Running exchange rate update... >> "%LOG%"
"%PHP_EXE%" "%SCRIPT%" >> "%LOG%" 2>&1
echo [%DATE% %TIME%] Done. >> "%LOG%"
