<div align="center">

<img src=".github/assets/logo.png" width="120" alt="SHM System logo">

# SHM System

**Membership and finance management for**<br>
**សមាគមសង្គហៈមិត្ត បុគ្គលិក មន្ត្រីរាជការ — ក្រសួងសេដ្ឋកិច្ចនិងហិរញ្ញវត្ថុ**

<br>

![Laravel](https://img.shields.io/badge/Laravel-13-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-8.3+-777BB4?style=for-the-badge&logo=php&logoColor=white)
![Vue.js](https://img.shields.io/badge/Vue.js-2.7-4FC08D?style=for-the-badge&logo=vuedotjs&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-8-4479A1?style=for-the-badge&logo=mysql&logoColor=white)
![Firebase](https://img.shields.io/badge/Firebase-FCM-FFCA28?style=for-the-badge&logo=firebase&logoColor=black)

[Features](#features) •
[Getting started](#getting-started) •
[Commands](#common-commands) •
[Guides](#guides) •
[Deployment](#deployment)

</div>

---

## Features

- **Members (សមាជិក)** — registration, documents, requests, import / export
- **Payments** — monthly payables, recurring payments, payment history
- **Incentives (ប្រាក់ឧបត្ថម្ភ)** — incentive records and official letters / annexes to banks, GDNT and LM
- **Finance** — income, expenses, chart of accounts, bank accounts
- **Budget modules** — program management, budget arrangement (ceiling, PIP), budget monitoring
- **Reports (របាយការណ៍)** — Khmer PDF reports with filters and download
- **Notifications** — in-app and mobile push (Firebase Cloud Messaging)
- **Mobile API** — login with OTP and dashboard endpoints under `/api/v1/mobile`
- **Access control** — roles and permissions, audit log

## Tech stack

| Layer | Technology |
|---|---|
| Backend | PHP 8.3+, Laravel 13, Laravel Passport (API auth), Spatie Permission |
| Frontend | Vue 2.7, Vuesax / Vuexy, Laravel Mix (webpack) |
| Database | MySQL (tables use the `tbl_` prefix) |
| PDF / Excel | mPDF, Maatwebsite Excel |
| Push | Firebase (kreait/laravel-firebase) |

## Requirements

- PHP 8.3 or newer, with `mbstring`, `gd`, `zip`, `pdo_mysql`
- Composer 2
- MySQL 8 (or MariaDB 10.6+)
- Node.js and npm

## Getting started

```bash
git clone https://github.com/StarCodeKh/shm-system.git
cd shm-system

composer install
npm install

cp .env.example .env
php artisan key:generate
php artisan passport:keys
```

1. **Database** — create a database (default name `shm`), set the `DB_*` values in `.env`, then import the latest SQL dump from the team. Migrations only cover part of the schema, so start from a dump.
2. **Firebase** — put the service account JSON in the project root as `service-account-key.json`, and a copy in `storage/firebase/firebase_credentials.json`. Fill in the `FIREBASE_*` and `FCM_*` values in `.env`.
3. **Run** — in two terminals:

```bash
php artisan serve      # http://127.0.0.1:8000
npm run watch          # rebuilds the frontend on change
```

> Secrets (`.env`, `service-account-key.json`, `storage/firebase/`, `storage/oauth-*.key`) are git-ignored. Never commit them.

## Common commands

| Command | Purpose |
|---|---|
| `npm run dev` | Build frontend once (development) |
| `npm run watch` | Build and watch for changes |
| `npm run prod` | Minified production build |
| `php artisan route:list --except-vendor` | List API routes |
| `php artisan optimize:clear` | Clear config, route and view caches |
| `php artisan schedule:work` | Run the scheduler locally |

## Scheduled jobs

Defined in `app/Console/Kernel.php`:

| Command | When |
|---|---|
| `payables:generate` | 1st of each month, 01:00 — creates monthly payables |
| `payments:dispatch` | Hourly, 02:00–23:00 — queues pending payments |
| `queue:work --queue=payments,default` | Every minute — processes queued jobs |

On a server, add one cron entry:

```cron
* * * * * cd /path/to/shm-system && php artisan schedule:run >> /dev/null 2>&1
```

## Project structure

```
app/
  Http/Controllers/     API controllers (Settings/, Modules/ for budget modules)
  Models/               Eloquent models
  Console/Commands/     Scheduled commands
config/pdf.php          PDF font settings
resources/
  assets/fonts/         All fonts (self-hosted, no CDN)
  js/src/               Vue app — router.js, apiConfig.js, views/pages/
  sass/vuexy/_fonts.scss  Font declarations and CSS variables
  views/reports/        PDF report templates
routes/
  api.php               API routes (/api/auth, /api/v1, /api/v1/mobile)
  web.php               Single-page app entry
```

## Guides

### Fonts

All fonts are local in `resources/assets/fonts` and declared once in `resources/sass/vuexy/_fonts.scss`. Use the CSS variables instead of font names:

```css
font-family: var(--font-base);      /* UI — Kantumruy Pro */
font-family: var(--font-document);  /* letters — Khmer OS Battambang */
```

Other variables: `--font-heading`, `--font-mef1`, `--font-mef2`, `--font-fk1`, `--font-fk2`, `--font-mono`. PDF fonts are set in `config/pdf.php`.

### Adding a PDF report

Add an entry to `reports()` in `app/Http/Controllers/ReportController.php` (title, icon, color, columns, query). It then appears on the **/report** page and is served at `POST /api/v1/report/{key}`. No new route or template is needed.

## Deployment

```bash
composer install --no-dev --optimize-autoloader
npm ci && npm run prod
php artisan config:cache && php artisan route:cache && php artisan view:cache
```

Copy these to the server by hand: `.env`, `service-account-key.json`, `storage/firebase/firebase_credentials.json`, the Passport keys in `storage/`, and the `public/upload/` folder. Point the web server at `public/`.

## License

Proprietary. All rights reserved by សមាគមសង្គហៈមិត្ត បុគ្គលិក មន្ត្រីរាជការ, Ministry of Economy and Finance.
