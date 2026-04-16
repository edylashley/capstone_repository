<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework. You can also check out [Laravel Learn](https://laravel.com/learn), where you will be guided through building a modern Laravel application.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## ClamAV (Windows) — Local testing guide ✅

If you want to enable file scanning locally (recommended for testing the upload blocking behavior), install ClamAV and ensure `clamscan` is on your PATH or set the `CLAMSCAN_PATH` env value to the full executable path.

Quick install (Windows via Chocolatey):

1. Open an elevated PowerShell prompt (Run as Administrator)
2. Install Chocolatey (if not already installed):
   - Set-ExecutionPolicy Bypass -Scope Process -Force; [System.Net.ServicePointManager]::SecurityProtocol = [System.Net.ServicePointManager]::SecurityProtocol -bor 3072; iex ((New-Object System.Net.WebClient).DownloadString('https://community.chocolatey.org/install.ps1'))
3. Install ClamAV:
   - choco install clamav -y
4. Update virus definitions and verify `clamscan` is available:
   - freshclam
   - where clamscan  # confirm path

Configuration in `.env` (example):

FILESCAN_ENABLED=true
CLAMSCAN_PATH=clamscan

How to confirm it works:

- Start the app: `php artisan serve` and attempt a manuscript upload as a student.
- If `clamscan` finds an issue, the upload will be blocked (HTTP 422) and the response will include:
  - `{ "message": "File scan failed: upload blocked", "notes": "..." }`
- Check logs (`storage/logs/laravel.log`) and the activity log database table for `manuscript_scan_failed` entries.

If you prefer not to install ClamAV yet, a small dev script is included to simulate `clamscan` exit codes so you can test failure behavior without installing ClamAV.

Fake scanner (dev):

- Path: `dev-scripts/fake-clamscan.bat` (Windows)
- Behavior: returns a non-zero exit code when the uploaded filename contains the words `infect` / `infected` or `eicar`. For example, uploading `infected.pdf` will simulate a detection and cause the upload to be blocked.

Use this by setting in `.env`:

```
FILESCAN_ENABLED=true
CLAMSCAN_PATH=dev-scripts\\fake-clamscan.bat
```

---

## Quick start script (Windows PowerShell)

A convenience script is provided to prepare and run the application locally:

- Path: `dev-scripts/start-local.ps1`
- Usage:

```
# runs composer install (unless skipped), runs migrations/seeds, creates storage link, and starts the dev server
powershell -ExecutionPolicy Bypass -File dev-scripts\start-local.ps1

# optional: skip Composer install or migrations
powershell -ExecutionPolicy Bypass -File dev-scripts\start-local.ps1 -SkipComposer -SkipMigrate
```

Notes:
- The script will exit early if `php` is not found on your PATH. Ensure PHP is installed and accessible.
- It runs `php artisan migrate --seed --force` by default to ensure seeded test users exist. Use `-SkipMigrate` to avoid destructive operations if your DB is already prepared.

---

Add your official system logo

To use a custom logo (like the one you provided) place the image file at `public/images/norsu-bsint-logo.png`. The `application-logo` component will automatically use this image when present and fall back to the built-in SVG otherwise.

Recommended: use a PNG or SVG with a transparent background, approximately 240px wide (the component scales down to fit header sizes).

---

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
