# Fix ClamAV Daemon Windows Service
# The issue: clamd takes ~30-60s to load its virus DB, exceeding the default Windows service startup timeout.
# Solution: Use NSSM (Non-Sucking Service Manager) or just run clamd in background via Task Scheduler.

# Remove the broken service first
$serviceName = "clamd"
$existing = Get-Service -Name $serviceName -ErrorAction SilentlyContinue
if ($existing) {
    Stop-Service $serviceName -Force -ErrorAction SilentlyContinue
    sc.exe delete $serviceName
    Start-Sleep -Seconds 2
    Write-Host "Removed broken service." -ForegroundColor Yellow
}

# Instead, create a Scheduled Task that runs at startup (no timeout issues!)
$taskName = "ClamAV Daemon"
$exePath = "C:\Program Files\ClamAV\clamd.exe"
$confPath = "C:\Program Files\ClamAV\clamd.conf"
$arguments = "--config-file=`"$confPath`""

# Remove any existing task
Unregister-ScheduledTask -TaskName $taskName -Confirm:$false -ErrorAction SilentlyContinue

# Create the action
$action = New-ScheduledTaskAction -Execute $exePath -Argument $arguments

# Trigger: at system startup
$trigger = New-ScheduledTaskTrigger -AtStartup

# Run as SYSTEM so it works even without logging in
$principal = New-ScheduledTaskPrincipal -UserId "SYSTEM" -LogonType ServiceAccount -RunLevel Highest

# Settings: allow it to run indefinitely, restart on failure
$settings = New-ScheduledTaskSettingsSet `
    -AllowStartIfOnBatteries `
    -DontStopIfGoingOnBatteries `
    -ExecutionTimeLimit ([TimeSpan]::Zero) `
    -RestartCount 3 `
    -RestartInterval (New-TimeSpan -Minutes 1)

# Register the task
Register-ScheduledTask `
    -TaskName $taskName `
    -Action $action `
    -Trigger $trigger `
    -Principal $principal `
    -Settings $settings `
    -Description "Runs ClamAV daemon (clamd) at startup for fast virus scanning on localhost:3310"

Write-Host ""
Write-Host "Scheduled Task '$taskName' created!" -ForegroundColor Green

# Start it now
Write-Host "Starting ClamAV Daemon now..." -ForegroundColor Cyan
Start-ScheduledTask -TaskName $taskName

# Wait for it to load the database
Write-Host "Waiting for daemon to load virus database (this takes ~30 seconds)..." -ForegroundColor Yellow
Start-Sleep -Seconds 35

# Verify it's running by trying clamdscan
$result = & "C:\Program Files\ClamAV\clamdscan.exe" --ping 3 2>&1
Write-Host ""
if ($LASTEXITCODE -eq 0) {
    Write-Host "SUCCESS! ClamAV Daemon is running and responding!" -ForegroundColor Green
    Write-Host "It will auto-start every time your PC boots. No terminal needed!" -ForegroundColor Green
} else {
    Write-Host "Daemon may still be loading. Try running this in a minute:" -ForegroundColor Yellow
    Write-Host '  & "C:\Program Files\ClamAV\clamdscan.exe" --ping 3' -ForegroundColor White
}

Write-Host ""
Write-Host "Press any key to close..." -ForegroundColor Gray
$null = $Host.UI.RawUI.ReadKey("NoEcho,IncludeKeyDown")
