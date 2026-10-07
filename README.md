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
| `OTP_EXPOSE_CODE` | `true` (until SMS is connected) | `false` |
| `CORS_ALLOWED_ORIGINS` | empty | empty, unless another site must call the API |

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

Reviewed and fixed on 2026-10-07. Keep this section current: move an item to **In place** when it is done.

**In place**

- Secrets stay out of git: `.env`, `service-account-key.json`, `storage/firebase/`, Passport keys (`storage/oauth-*.key`)
- Every API route needs a token (`auth:api`) except the four sign-in endpoints under `/api/auth/*`
- Rate limits: sign-in 10 attempts a minute, the rest of the API 60 requests a minute
- Permissions: create / update / delete on resource routes check the controller's permission (`EnforceModulePermission`); setting a member's login, member import and income import check theirs too
- Requests cannot set `id`, timestamps or the audit columns `created_by` / `modified_by`; a member's link to a login account (`officer.user_id`) changes only through the permission-checked endpoint
- Sign-in tokens expire: 24 hours on the web, 30 days with "remember me" and on the mobile app (`AUTH_TOKEN_HOURS`, `AUTH_TOKEN_REMEMBER_DAYS`)
- Mobile OTP: 6 random digits from a secure generator, single use, expires per `config_otp_expire`; the user's password is never changed; the request answers the same for unknown numbers; the code is only returned when `OTP_EXPOSE_CODE=true` (test servers)
- Uploads: only images, PDF, Word, Excel and CSV (checked by content, max 20 MB; imports max 10 MB); the server picks the file name; the web server refuses to run scripts in `public/images/uploads/` and `public/upload/` (`public/.htaccess`, `public/web.config`)
- CORS: only `APP_URL` and `CORS_ALLOWED_ORIGINS`, without credentials
- Security headers on every response: `X-Content-Type-Options`, `X-Frame-Options`, `Referrer-Policy`, `Permissions-Policy`, a Content-Security-Policy for framing / plugins / forms, and HSTS over HTTPS
- SQL: report queries pass values as bound parameters
- No debug output in responses; logs never contain tokens or passwords; production bundles leave out `console.log`
- `composer audit`: no known advisories. `npm audit`: nothing in the browser bundle (see below)

**Open**

| Severity | Issue | Where | What is needed |
|---|---|---|---|
| High | Mobile OTP codes are not delivered by SMS; on production (`OTP_EXPOSE_CODE=false`) mobile OTP sign-in cannot complete | `OTPService::sentOTP` | Connect an SMS provider and send the code there |
| High | The admin password that was in the sign-in form (`LoginJWT.vue`, in git history) is still the live password | user `admin@admin.com` | Change it on every server |
| Medium | Old keys are still in git history (Firebase demo config, a Google Maps key) | git history | Revoke / rotate those keys in their consoles |
| Low | Most models accept any column the form sends (only id, timestamps, audit columns and `officer.user_id` are blocked) | models with `$guarded = []`, controllers' `dataForm()` | List the allowed columns per model (`$fillable`) |
| Low | The Content-Security-Policy does not restrict scripts | `SecurityHeaders` | Add nonces to the inline `<script>` blocks in `application.blade.php`, then a `script-src` rule |
| Info | `npm audit`: 7 build-time advisories in Tailwind CSS 3 tooling (`braces`, `chokidar`, `fast-glob`, `micromatch`, `postcss-nested`); none reach the browser | `package.json` | Upgrade to Tailwind 4 when convenient |

**Fixed on 2026-10-07**

- Any signed-in account could set another user's password (`update_officer_user`): now needs the member update permission and only touches that member's own login
- Mobile OTP code was returned in every response, and a successful OTP replaced the user's password
- Uploaded files kept their own name and extension under `public/` (a script could be uploaded and run)
- Member and income spreadsheet imports had no permission or file-type check
- The sign-in form was pre-filled with an admin email and password (now only with `npm run dev`)
- The push service printed the Firebase access token into API responses; a test sender with a hard-coded device token was removed
- CORS allowed every origin with credentials
- Tokens lasted 1 year and "remember me" did not change the real expiry
- No security headers
- Report SQL built by joining strings (one query also never matched; another ignored its entity filter)
- The OTP request told whether a phone number has an account
- `npm audit` went from 169 issues (6 critical) to 7 build-time ones: removed 21 unused Vue 2 / Laravel Mix packages, upgraded `axios` to 1.x and SheetJS (`xlsx`) to 0.20.3

**Before going live**

- `.env`: `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL` with `https://`, `LOG_STACK=daily`, `OTP_EXPOSE_CODE=false`, `CORS_ALLOWED_ORIGINS` only if another site must call the API (see [Deployment](#deployment))
- Serve over HTTPS only
- Change the admin password; rotate the keys listed under **Open**
- On nginx (no `.htaccess`): deny `\.php$` under `/images/uploads/` and `/upload/`, and add the security headers for static files

## License

Proprietary. All rights reserved by សមាគមសង្គហៈមិត្ត បុគ្គលិក មន្ត្រីរាជការ, Ministry of Economy and Finance.
