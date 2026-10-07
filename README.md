<div align="center">

<img src=".github/assets/logo.png" width="120" alt="SHM System logo">

# SHM System

**Membership and finance management for**<br>
**សមាគមសង្គហៈមិត្ត បុគ្គលិក មន្ត្រីរាជការ — ក្រសួងសេដ្ឋកិច្ចនិងហិរញ្ញវត្ថុ**

<br>

![Laravel](https://img.shields.io/badge/Laravel-13-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-8.3+-777BB4?style=for-the-badge&logo=php&logoColor=white)
![Vue.js](https://img.shields.io/badge/Vue.js-3.5-4FC08D?style=for-the-badge&logo=vuedotjs&logoColor=white)
![Vite](https://img.shields.io/badge/Vite-8-646CFF?style=for-the-badge&logo=vite&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-8-4479A1?style=for-the-badge&logo=mysql&logoColor=white)
![Firebase](https://img.shields.io/badge/Firebase-FCM-FFCA28?style=for-the-badge&logo=firebase&logoColor=black)

[Features](#features) •
[Getting started](#getting-started) •
[Commands](#common-commands) •
[Guides](#guides) •
[Deployment](#deployment) •
[Security](#security)

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
| Frontend | Vue 3.5, Vue Router 5, Vuex 4, vue-i18n 11, Vite 8 (`laravel-vite-plugin`), Tailwind 3; Vuesax-style UI components in `resources/js/src/ui/` |
| Database | MySQL (tables use the `tbl_` prefix) |
| PDF / Excel | mPDF, Maatwebsite Excel |
| Push | Firebase (kreait/laravel-firebase) |

## Requirements

- PHP 8.3 or newer, with `mbstring`, `gd`, `zip`, `pdo_mysql`
- Composer 2
- MySQL 8 (or MariaDB 10.6+)
- Node.js 20+ and npm

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

1. **Database** — create a database (default name `shm_system_db`), set the `DB_*` values in `.env`, then import the latest SQL dump from the team. Migrations only cover part of the schema, so start from a dump.
2. **Firebase** — put the service account JSON in the project root as `service-account-key.json`, and a copy in `storage/firebase/firebase_credentials.json`. Fill in the `FIREBASE_*` and `FCM_*` values in `.env`.
3. **Run** — in two terminals:

```bash
php artisan serve      # http://127.0.0.1:8000
npm run dev            # Vite dev server: changes show instantly
```

> **"Vite manifest not found"** means the frontend was never built and `npm run dev` is not running. Start `npm run dev`, or run `npm run build` once.

> Secrets (`.env`, `service-account-key.json`, `storage/firebase/`, `storage/oauth-*.key`) are git-ignored. Never commit them.

## Common commands

| Command | Purpose |
|---|---|
| `npm run dev` | Vite dev server with hot reload (local only) |
| `npm run build` | Production build into `public/build/` |
| `npm run watch` | Rebuild `public/build/` on every change |
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

The same steps for **UAT** and **production (PRD)**; only `.env` differs.

### 1. `.env` on the server

Copy the UAT / production block from `.env.example`:

| Key | UAT | Production |
|---|---|---|
| `APP_ENV` | `uat` | `production` |
| `APP_DEBUG` | `false` | `false` |
| `APP_URL` | `https://…` | `https://…` |
| `LOG_STACK` | `daily` | `daily` |
| `LOG_LEVEL` | `info` | `warning` |
| `LOG_DAILY_DAYS` | `14` | `30` |

`APP_DEBUG=true` on a server shows code, settings and SQL to anyone who triggers an error.

### 2. Deploy

```bash
git pull
composer install --no-dev --optimize-autoloader
npm ci
npm run build                      # writes public/build/ (not in git)
php artisan migrate --force
php artisan optimize               # caches config, routes, events, views
php artisan queue:restart          # workers pick up the new code
```

`npm run build` leaves `console.log` / `debug` / `info` out of the bundle (see `vite.config.mjs`); `console.error` and `console.warn` stay so real failures still reach the browser console.

### 3. What goes to the server and what doesn't

| Copy by hand (secrets, not in git) | Never upload |
|---|---|
| `.env` | `node_modules/` |
| `service-account-key.json` | `public/hot` (only exists while `npm run dev` runs; its presence makes Laravel load the dev server) |
| `storage/firebase/firebase_credentials.json` | `storage/logs/*` from a developer machine |
| Passport keys `storage/oauth-*.key` | local `.env` |
| `public/upload/` and `public/images/uploads/` (user files) | |

Point the web server at `public/`.

### 4. Logs

- Laravel writes to `storage/logs/`. With `LOG_STACK=daily` there is one file per day (`laravel-YYYY-MM-DD.log`) and files older than `LOG_DAILY_DAYS` are deleted automatically.
- To empty the logs by hand: `rm storage/logs/*.log` (the folder must stay writable by the web server).
- Code never prints debug output into responses (`echo`, `print_r`, `dd`, `dump`); use `Log::info()` / `Log::error()` and never log tokens or passwords.

### 5. After deploying

```bash
php artisan about                  # environment, debug mode, cache and log driver at a glance
php artisan optimize:clear         # only if a change in .env or config does not show
```

## Security

Reviewed on 2026-10-07. Update the **Status** column as issues are fixed.

**In place**

- `.env`, `service-account-key.json` and the Passport keys are not in git; key files are readable by their owner only
- Only the four sign-in endpoints (`/api/auth/*`) work without a token; every other route needs `auth:api`
- Sign-in is limited to 10 attempts a minute, the rest of the API to 60 requests a minute
- Create / update / delete on resource routes check the controller's permission (`EnforceModulePermission`)
- `composer audit`: no known advisories

**Open issues**

| Severity | Issue | Where | Status |
|---|---|---|---|
| Critical | Setting a member's login password has no permission or ownership check, so any signed-in account can change another user's password | `OfficerController::updateIncentiveOfficer` (`POST /api/v1/update_officer_user`) | Open |
| Critical | The mobile OTP code is returned in the API response and no SMS is sent, so the code does not prove who is signing in | `AuthController::loginMobileOTP`, `OTPService` | Open — needs an SMS provider |
| Critical | Uploaded files keep their own name and extension and are saved under `public/`, so a script file can be uploaded and run | `FileManagerController::fileImageUploadService` | Open |
| High | A successful OTP sign-in replaces the user's password with the OTP code | `OTPService::checkOTP` | Open |
| High | Member spreadsheet import has no permission check and no file-type check | `FileManagerController::fileUploadService` (`POST /api/v1/file_upload`) | Open |
| High | The sign-in form was pre-filled with an admin email and password | `LoginJWT.vue` | Fixed 2026-10-07: pre-filled only with `npm run dev`, not in production builds. Still change that admin password |
| Medium | CORS allows every origin together with credentials | `config/cors.php` | Open |
| Medium | Access tokens last 1 year; "remember me" shortens them to 1 week instead of extending them | `AuthController` sign-in methods | Open |
| Medium | 31 models use `$guarded = []` and controllers save the whole request, so extra columns can be set | models, controllers' `dataForm()` | Open |
| Medium | No security headers (`X-Frame-Options`, `Content-Security-Policy`, `X-Content-Type-Options`, HSTS) | `public/.htaccess` / middleware | Open |
| Low | Template leftovers: unused Firebase demo config and a Google Maps key in a comment (still in git history) | `resources/js/src/firebase/`, `main.js` | Fixed 2026-10-07 (removed from the code; still in git history) |
| High | The push-notification service printed the Firebase access token into API responses | `PushNotificationService::generateAccessToken` | Fixed 2026-10-07: errors go to the log, the token is never printed or logged |
| Low | Three report queries build SQL by joining strings (not exploitable today; values are dates or database ids) | `ReportController` | Open |
| Low | The OTP request says whether a phone number has an account | `AuthController::loginMobileOTP` | Open |
| Info | `npm audit` listed 169 issues (6 critical), mostly in build tools and the unused template Firebase login | `package.json` | Re-check: the Firebase login was removed and the build moved to Vite, but the old Laravel Mix packages (`laravel-mix`, `*-loader`, `vue-template-compiler`) are still in `package.json`; remove them, then run `npm audit` again |

**Before going live**

- `.env`: `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL` with `https://`, `LOG_STACK=daily` (see [Deployment](#deployment))
- Serve over HTTPS only
- Make sure the web server does not run scripts inside `public/images/uploads/`
- Fix the Critical and High issues above

## License

Proprietary. All rights reserved by សមាគមសង្គហៈមិត្ត បុគ្គលិក មន្ត្រីរាជការ, Ministry of Economy and Finance.
