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

One-time setup of an Ubuntu server, also one that already hosts other sites, e.g. `app-v2.shm-mef.org` in `/var/www/html/app-v2/shm-system`. Then follow [Deployment](#deployment).

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
php artisan passport:keys          # first install only, if storage/oauth-*.key do not exist
sudo chmod 660 storage/oauth-*.key # Passport refuses keys readable by others (after every chmod -R)
ls -l storage/oauth-*.key          # -rw-rw---- soengsouy www-data
chmod 640 .env
```

**4. Certificate**: HTTPS on this server uses the certificate for `*.shm-mef.org` (`/etc/ssl/certs/shm-mef.org.pem`, the same one `www` and `dbadmin` use). There is no Let's Encrypt / Certbot on this server. Put its two lines in one shared file that every site can include:

```bash
sudo openssl x509 -noout -subject -enddate -ext subjectAltName -in /etc/ssl/certs/shm-mef.org.pem   # must list *.shm-mef.org, date in the future
sudo grep -E "^\s*ssl_certificate(_key)?\s" /etc/nginx/sites-available/www.shm-mef.org | sed 's/^\s*//' | sudo tee /etc/nginx/sites-available/00-fallback-ssl
sudo ln -sf /etc/nginx/sites-available/00-fallback-ssl /etc/nginx/sites-enabled/00-fallback-ssl
```

Enabled, it is also the fallback for older sites whose `listen 443 ssl` has its certificate lines commented out (`app`, `meeting`, `pensioner`); without it nginx refuses to load any change (`no "ssl_certificate" is defined`). Their own files stay untouched.

In Cloudflare, *SSL/TLS › Overview* must be **Full (strict)**; "Flexible" causes redirect loops.

**5. nginx**: each site has its own file in `/etc/nginx/sites-available/`. The old system `app.shm-mef.org` (proxied to port 8088) keeps its file untouched; this app gets `/etc/nginx/sites-available/app-v2.shm-mef.org`.

```bash
sudo cp -r /etc/nginx/sites-available ~/nginx-backup-$(date +%F)   # backup first
sudo nano /etc/nginx/sites-available/app-v2.shm-mef.org
```

Paste this, save with `Ctrl+O` `Enter`, exit with `Ctrl+X`:

```nginx
server {
    listen 80;
    server_name app-v2.shm-mef.org;
    return 301 https://$host$request_uri;
}

server {
    listen 443 ssl http2;
    server_name app-v2.shm-mef.org;

    # same *.shm-mef.org certificate as www / dbadmin (step 4)
    include /etc/nginx/sites-available/00-fallback-ssl;

    root /var/www/html/app-v2/shm-system/public;
    index index.php;
    charset utf-8;
    client_max_body_size 20M;

    access_log /var/log/nginx/app-v2-access.log;
    error_log  /var/log/nginx/app-v2-error.log;

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
}
```

Laravel runs through PHP-FPM: no `proxy_pass`, no port, no `php artisan serve`. The `443` block is required: Cloudflare connects over HTTPS, and without it the request lands on another site's 443 block.

```bash
ls /run/php/                                     # the socket must match fastcgi_pass
sudo systemctl enable --now php8.5-fpm
sudo ln -sf /etc/nginx/sites-available/app-v2.shm-mef.org /etc/nginx/sites-enabled/
ls -l /etc/nginx/sites-enabled/                  # only the sites that should be live, each a link
sudo nginx -t                                    # must say "syntax is ok" and "test is successful"
sudo systemctl reload nginx                      # only after a successful test; a failed one changes nothing
sudo nginx -T 2>/dev/null | grep -c "app-v2.shm-mef.org"   # 1 or more = loaded
curl -s -o /dev/null -w "app-v2: %{http_code}\n" -m 10 https://app-v2.shm-mef.org
curl -s -o /dev/null -w "app: %{http_code}\n"    -m 10 https://app.shm-mef.org   # old system still answers
```

Never `restart` or `stop` nginx on this server while `nginx -t` fails: every site would go down.

**6. Cron**: `crontab -e` as the login user (runs the scheduler and the queue worker):

```
* * * * * cd /var/www/html/app-v2/shm-system && php artisan schedule:run >> /dev/null 2>&1
```

**7. App settings**: production values in `.env`, then rebuild the caches. Run after every `.env` change.

```bash
cd /var/www/html/app-v2/shm-system
sed -i 's/^APP_DEBUG=.*/APP_DEBUG=false/; s/^APP_ENV=.*/APP_ENV=production/; s|^APP_URL=.*|APP_URL=https://app-v2.shm-mef.org|' .env
grep -E '^(APP_ENV|APP_DEBUG|APP_URL|DB_USERNAME)=' .env    # production, false, https://..., shm_user
php artisan config:clear
php artisan migrate --force
php artisan optimize
sudo systemctl reload php8.5-fpm                               # drop PHP's cached code (OPcache)
```

**8. First users** (new install only): in production the seeder needs a password from `.env`; it never uses a default there.

```bash
echo 'SEED_USER_PASSWORD="Your-Strong-Password-2026"' >> .env   # no spaces around =
php artisan config:clear                                       # a cached config hides .env from the seeder
php artisan tinker --execute="var_dump(env('SEED_USER_PASSWORD'));"   # must print string(...), not NULL
php artisan db:seed --class=UserSeeder --force                  # prints the 3 accounts it created
php artisan optimize
```

Sign in at https://app-v2.shm-mef.org as `superadmin@system.com`, change the three passwords in the app, then empty `SEED_USER_PASSWORD=` in `.env` and run `php artisan optimize`. The seeder only creates users while the users table is empty.

A plain "500 Server Error" page: read `tail -30 storage/logs/laravel.log`.

**Troubleshooting**

| Error | Fix |
|---|---|
| `composer: command not found` | Install Composer (step 1) |
| `vendor does not exist and could not be created`, npm `EACCES` | Wrong owner (step 3) |
| `1698 Access denied for user 'root'@'localhost'` | Use the app's own DB user (step 2), then `php artisan config:clear` |
| `View path not found` on `optimize` | `storage/framework/views` is missing (step 3) |
| `Please provide a valid cache path` (500) | `storage/framework/*` folders are missing or not writable (step 3), then `php artisan optimize` |
| `Key file ... oauth-public.key permissions are not correct` (500 on every API call, nobody can sign in) | `chmod 660 storage/oauth-*.key` (step 3) |
| `Users not created: set SEED_USER_PASSWORD in .env` | Add the line, then `php artisan config:clear` before seeding (step 8) |
| Laravel error page with code and paths visible to everyone | `APP_DEBUG=true` on the server: set `APP_DEBUG=false`, then `php artisan optimize` |
| `Another route has already been assigned name [...]` on `route:cache` | Two routes share a name; give one `->names('prefix.resource')` |
| `Welcome to nginx!` or `404 Not Found` | The app-v2 file is not loaded yet: `sudo nginx -t`, fix the error it names, reload (step 5) |
| `Job for nginx.service failed` on reload | nginx is stopped: `sudo nginx -t`, then `sudo systemctl start nginx`. If the test fails, the error names the file (check `conf.d/` too) |
| `bind() ... (98: Address already in use)` | Another program holds port 80/443: `sudo ss -ltnp \| grep -E ':80 \|:443 '` |
| `no "ssl_certificate" is defined` | An enabled site has `listen 443 ssl` without certificate lines: enable `00-fallback-ssl` (step 4) |
| `unknown directive "http2"` | Older nginx: use `listen 443 ssl http2;` and no separate `http2 on;` line |
| Django `Bad Request (400)` on app-v2 | The app-v2 file has no `listen 443 ssl` block, so the old system answers (step 5) |
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
