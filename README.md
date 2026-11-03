<div align="center">

<img src=".github/assets/logo.png" width="120" alt="SHM System logo">

# SHM System

**Membership and finance management for**<br>
**សមាគមសង្គហៈមិត្ត បុគ្គលិក មន្ត្រីរាជការ — ក្រសួងសេដ្ឋកិច្ចនិងហិរញ្ញវត្ថុ**

![Laravel](https://img.shields.io/badge/Laravel-13-FF2D20?style=flat-square&logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-8.3+-777BB4?style=flat-square&logo=php&logoColor=white)
![Vue.js](https://img.shields.io/badge/Vue.js-3.5-4FC08D?style=flat-square&logo=vuedotjs&logoColor=white)
![Vite](https://img.shields.io/badge/Vite-8-646CFF?style=flat-square&logo=vite&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-8-4479A1?style=flat-square&logo=mysql&logoColor=white)

</div>

## Features

- **Members (សមាជិក)** — registration, documents, requests, Excel import / export
- **Payments** — monthly payables, recurring payments, payment history
- **Incentives (ប្រាក់ឧបត្ថម្ភ)** — incentive records per member, entity and bank
- **Finance** — income, expenses, chart of accounts
- **Reports (របាយការណ៍)** — Khmer PDF reports with filters
- **Notifications** — in-app and mobile push (Firebase Cloud Messaging)
- **Mobile API** — OTP sign-in and dashboard under `/api/v1/mobile`
- **Access** — three roles (Super Admin, Admin, Normal User), permissions per role, audit log
- **Sidebar menu** — stored in the database, edited in *Administration › Menus*, filtered by permission
- **File manager** — uploads stored on Laravel's `public` disk, with rename, caption and delete
- **Khmer and English** — every screen, switchable at any time

## Requirements

- PHP 8.3+ with `mbstring`, `gd`, `zip`, `pdo_mysql`, `xml`, `curl`, `fileinfo`, `openssl`
- Composer 2, Node.js 20+ and npm
- MySQL 8 (or MariaDB 10.6+), `utf8mb4`

## Getting started

```bash
git clone https://github.com/StarCodeKh/shm-system.git && cd shm-system
composer install && npm install
cp .env.example .env               # set DB_* (database must exist), SEED_USER_PASSWORD
php artisan key:generate
php artisan passport:keys
php artisan migrate --seed         # schema + settings, permissions, roles, menu, first users
php artisan storage:link           # serves uploaded files at /storage/...
```

Run it in two terminals: `php artisan serve` (http://127.0.0.1:8000) and `npm run dev`.

On a new install (empty users table) the seeders create one user per role: `superadmin@system.com`, `admin@system.com`, `user@system.com`.
Their password is `SEED_USER_PASSWORD`; outside production it defaults to `Password@123`. Change it after the first sign-in.

> **"Vite manifest not found"** — start `npm run dev`, or run `npm run build` once.
> Secrets (`.env`, `storage/firebase/`, `storage/oauth-*.key`) are git-ignored; never commit them.

## Common commands

| Command | Purpose |
|---|---|
| `npm run dev` / `npm run build` | Vite dev server / production build into `public/build/` |
| `php artisan migrate --seed` | Create or update the schema and fill in missing defaults (safe to repeat) |
| `php artisan db:seed` | Add missing settings, permissions, roles and menu items only; never overwrites edits |
| `php artisan db:import-shm <dump.sql>` | Import the old SHM system's PostgreSQL dump (see [Migrating](#migrating-from-the-old-shm-system)) |
| `php artisan optimize:clear` | Clear config, route and view caches |
| `php artisan schedule:work` | Run the scheduler locally |

Scheduled jobs (`app/Console/Kernel.php`): `payables:generate` on the 1st of each month at 01:00, `payments:dispatch` hourly, and the queue worker every minute.

## Project structure

```
app/Http/Controllers/        API controllers (Settings/ for setup screens)
app/Models/                  Eloquent models
app/Console/Commands/        Scheduled commands, db:import-shm
app/Services/ShmLegacy.php   Old SHM dump → new tables (the whole mapping in one file)
database/migrations/         The schema (one file per table)
database/seeders/            Defaults; data/*.php holds settings, permissions, roles, menu
resources/js/src/            Vue app: router.js, apiConfig.js, views/pages/, i18n/
resources/views/reports/     PDF report templates
routes/api.php               /api/auth, /api/v1, /api/v1/mobile
```

Code style: 4-space indents, UTF-8, no trailing spaces (`.editorconfig`). Each file keeps its own line endings (CRLF or LF).

## Deployment

Same steps for UAT and production; only `.env` differs. The web root is `public/`, served over HTTPS.

**First install**

```bash
cp .env.example .env               # APP_ENV, APP_DEBUG=false, APP_URL=https://..., DB_*, FIREBASE_*, SEED_USER_PASSWORD
composer install --no-dev --optimize-autoloader
php artisan key:generate           # first install only
npm ci && npm run build
php artisan migrate --seed --force
php artisan storage:link
php artisan optimize
```

Copy by hand, never through git: `storage/firebase/firebase_credentials.json` and the Passport keys `storage/oauth-*.key` (keep the same keys across deploys, or every user is signed out).
The web server user needs write access to `storage/` and `bootstrap/cache/` only.
Cron: `* * * * * cd /path/to/shm-system && php artisan schedule:run >> /dev/null 2>&1`

**Each deploy**

```bash
php artisan down
git pull
composer install --no-dev --optimize-autoloader
npm ci && npm run build
php artisan migrate --force && php artisan db:seed --force
php artisan optimize && php artisan queue:restart
php artisan up
```

Back up the database and `storage/app/public/` first. After any `.env` change run `php artisan optimize`.

**nginx** does not read `public/.htaccess`: route requests to `index.php` (`try_files $uri $uri/ /index.php?$query_string;`), set `client_max_body_size 20M`, and refuse scripts in `storage/uploads`, `images/uploads` and `upload`.

## Server setup (Ubuntu + nginx + Cloudflare)

One-time setup of a fresh Ubuntu server, e.g. `app-v2.shm-mef.org` in `/var/www/html/app-v2/shm-system`. Then follow [Deployment](#deployment).

**1. PHP, Composer, Node.js**

```bash
sudo add-apt-repository ppa:ondrej/php -y && sudo apt update
sudo apt install -y php8.5 php8.5-cli php8.5-fpm php8.5-mbstring php8.5-xml php8.5-curl \
  php8.5-zip php8.5-gd php8.5-mysql php8.5-bcmath php8.5-intl unzip
sudo update-alternatives --set php /usr/bin/php8.5

php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php composer-setup.php && sudo mv composer.phar /usr/local/bin/composer && rm composer-setup.php

curl -fsSL https://deb.nodesource.com/setup_lts.x | sudo -E bash - && sudo apt install -y nodejs
```

If a package does not support PHP 8.5 yet, install `php8.4-*` the same way and switch with `update-alternatives`.

**2. Database user**: Ubuntu's MySQL `root` signs in through the socket only (error `1698 Access denied`), so give the app its own user:

```sql
-- sudo mysql
CREATE DATABASE shm_system_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'shm_user'@'localhost' IDENTIFIED BY 'a-strong-password';
GRANT ALL PRIVILEGES ON shm_system_db.* TO 'shm_user'@'localhost';
FLUSH PRIVILEGES;
```

Set `DB_USERNAME=shm_user` and `DB_PASSWORD` in `.env`, then `php artisan config:clear`.

**3. Owner and permissions**: your login user owns the files and `www-data` (nginx / PHP-FPM) is the group. Never run `composer` or `npm` with `sudo`.

```bash
cd /var/www/html/app-v2/shm-system
sudo chown -R $USER:www-data .
mkdir -p storage/framework/{views,cache/data,sessions} storage/logs bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
chmod 640 .env
```

**4. nginx**: `/etc/nginx/sites-available/app-v2.shm-mef.org`

```nginx
server {
    listen 80;
    server_name app-v2.shm-mef.org;
    return 301 https://$host$request_uri;
}

server {
    listen 443 ssl;
    http2 on;
    server_name app-v2.shm-mef.org;
    root /var/www/html/app-v2/shm-system/public;
    index index.php;
    charset utf-8;
    client_max_body_size 20M;

    ssl_certificate     /etc/ssl/cloudflare/app-v2.shm-mef.org.pem;
    ssl_certificate_key /etc/ssl/cloudflare/app-v2.shm-mef.org.key;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    # uploads are never executed
    location ~ ^/(storage/uploads|images/uploads|upload)/.*\.php$ { deny all; }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/run/php/php8.5-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        fastcgi_hide_header X-Powered-By;
    }

    location ~ /\.(?!well-known) { deny all; }

    access_log /var/log/nginx/app-v2.access.log;
    error_log  /var/log/nginx/app-v2.error.log;
}
```

```bash
sudo ln -s /etc/nginx/sites-available/app-v2.shm-mef.org /etc/nginx/sites-enabled/
sudo rm -f /etc/nginx/sites-enabled/default      # otherwise "Welcome to nginx!" is served
sudo nginx -t && sudo systemctl reload nginx
```

**5. HTTPS through Cloudflare**: in Cloudflare, *SSL/TLS › Origin Server › Create Certificate*, save the certificate and key to `/etc/ssl/cloudflare/` (key `chmod 600`), and set *SSL/TLS › Overview* to **Full (strict)**. "Flexible" causes redirect loops.

**6. Cron**: `crontab -e` as the login user (runs the scheduler and the queue worker):

```
* * * * * cd /var/www/html/app-v2/shm-system && php artisan schedule:run >> /dev/null 2>&1
```

**Troubleshooting**

| Error | Fix |
|---|---|
| `composer: command not found` | Install Composer (step 1) |
| `vendor does not exist and could not be created`, npm `EACCES` | Wrong owner (step 3) |
| `1698 Access denied for user 'root'@'localhost'` | Use the app's own DB user (step 2), then `php artisan config:clear` |
| `View path not found` on `optimize` | `storage/framework/views` is missing (step 3) |
| `Another route has already been assigned name [...]` on `route:cache` | Two routes share a name; give one `->names('prefix.resource')` |
| `Welcome to nginx!` | Default site still enabled (step 4) |
| 502 Bad Gateway | PHP-FPM socket in nginx does not match the installed version: `ls /run/php/` |
| 500 error | `tail -f storage/logs/laravel.log` and `/var/log/nginx/app-v2.error.log` |

## Migrating from the old SHM system

The only data source is the old Django system's production `pg_dump` (`shm-mef.sql`), read directly; no PostgreSQL server is needed.
Settings and lists (units, benefit types, accounts, statuses, roles) come from the seeders; the dump brings members and their money.

```bash
php artisan migrate --force
php artisan db:import-shm /path/to/shm-mef.sql --dry-run   # check and count, saves nothing
php artisan db:import-shm /path/to/shm-mef.sql
```

| Old table | → New table |
|---|---|
| `customer_customer` | `officer` + `officer_document`, a yearly `officer_payment_recurring` each |
| `customer_receive` | `officer_payable` + `officer_payment` (cancelled → Void payable only), account 4100 / 4200 |
| `customer_payment` | `officer_incentive`, account 5101–5110 by benefit type |
| years not paid (active members) | `officer_payable` **Unpaid**, priced by `master_data_setprice` (36,000 → 60,000), so they show as owing and can pay |
| `auth_user` (staff who entered records) | `users`, **inactive with no password**, linked as `created_by` |
| business / operating units, benefit types | `entity`, `department`, `payment_type` (seeders) |

- **Chart of accounts** (`AccountSeeder`): `4000` contributions (`4100` yearly, `4200` registration) and `5000` benefits (`5101`–`5110`). The old system had no account codes, so these are ours.
- **Safe to repeat:** members are matched on their member number; imported records carry code `OLD`. `--fresh` re-imports them.
- **Not migrated:** logins, permissions, sessions and logs, plus the `*_bk` / `tmp_rec` backup copies (already in the live table).
- **Photos and scans are not in a dump:** copy the old server's `img/` and `doc/` folders to `public/images/uploads/legacy/`.
- `income` / `expense` stay empty: the dashboard counts member payments and benefits from their own tables.

## Security

- Every API route needs a token except sign-in; sign-in is limited to 10 attempts a minute, the API to 60 requests.
- Writes check the role's permission (`EnforceModulePermission`); requests cannot set `id`, timestamps or audit columns.
- Uploads: images, PDF, Word, Excel and CSV only (checked by content, max 20 MB), named by the server, never executed.
- Every sign-in (web, mobile, OTP) lasts `AUTH_TOKEN_DAYS` (90 days = 3 months), then the user signs in again; CORS allows `APP_URL` and `CORS_ALLOWED_ORIGINS` only; security headers on every response.

Before going live: `APP_DEBUG=false`, `OTP_EXPOSE_CODE=false`, HTTPS only, and change the seeded passwords.
Still open: mobile OTP codes are not sent by SMS yet (`OTPService`), and old keys in git history (Firebase demo config, a Google Maps key) should be rotated.

## License

Proprietary. All rights reserved by សមាគមសង្គហៈមិត្ត បុគ្គលិក មន្ត្រីរាជការ, Ministry of Economy and Finance.
