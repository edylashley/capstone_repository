<#
start-local.ps1 - helper to prepare and run the app locally (Windows PowerShell)

Usage:
  powershell -ExecutionPolicy Bypass -File .\dev-scripts\start-local.ps1

Options:
  -SkipComposer   Skip running `composer install` (useful if dependencies already installed)
  -SkipMigrate    Skip running migrations (useful if DB already prepared)
#>

param(
    [switch]$SkipComposer,
    [switch]$SkipMigrate
)

function Exec($cmd) {
    Write-Host "=> $cmd"
    $proc = Start-Process -FilePath "cmd.exe" -ArgumentList "/c", $cmd -NoNewWindow -Wait -PassThru
    if ($proc.ExitCode -ne 0) {
        Write-Error "Command failed with exit code $($proc.ExitCode): $cmd"
        exit $proc.ExitCode
    }
}

# Requirement checks
if (-not (Get-Command php -ErrorAction SilentlyContinue)) {
    Write-Error "php not found in PATH. Please install PHP and ensure 'php' is on PATH."
    exit 1
}

if (-not $SkipComposer) {
    if (Get-Command composer -ErrorAction SilentlyContinue) {
        Exec "composer install --no-interaction --prefer-dist"
    } else {
        Write-Host "composer not found; skipping 'composer install'. Run it manually if needed."
    }
}

if (-not $SkipMigrate) {
    Exec "php artisan migrate --seed --force"
}

# Ensure storage link exists
Exec "php artisan storage:link"

Write-Host "Starting dev server at http://127.0.0.1:8000"
Exec "php artisan serve --host=127.0.0.1 --port=8000"
