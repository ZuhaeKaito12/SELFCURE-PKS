# SELFCURE PKS

A web-based cybersecurity self-assessment system for Malaysian SMEs (PKS).
Business owners answer 17 plain-language questions across the five areas of the
NIST Cybersecurity Framework, get a maturity score, and follow a step-by-step
Action Plan. Administrators monitor registered SMEs, leave feedback and export
summary reports. The interface is available in English and Bahasa Melayu.

## Tech stack

- Laravel 12, PHP 8.2+
- MySQL 8 (sessions, cache and queue also use the database)
- Bootstrap 5.3, Chart.js and Intro.js from CDN, no Node.js build step
- DomPDF for PDF reports
- Google Gemini API for the AI assistant (optional; a rule-based summary is used without a key)

## Run locally

Requirements: PHP 8.2+ with `pdo_mysql`, `mbstring` and `dom`, Composer, and MySQL (e.g. XAMPP).

```bash
cp .env.example .env            # then fill in DB_* and ADMIN_PASSWORD
composer install
php artisan key:generate
php artisan migrate --seed      # tables + questions + the administrator account
php artisan serve               # http://127.0.0.1:8000
```

`php artisan db:seed` is safe to run again: it never deletes questions that
already exist and never resets an existing administrator.

## Deploy to Microsoft Azure

The site runs on **Azure App Service (Linux, PHP 8.3)** with **Azure Database
for MySQL – Flexible Server**. Deployment is automated with GitHub Actions
(`.github/workflows/azure-webapp.yml`).

1. **Create the database**: Azure Database for MySQL – Flexible Server.
   Create a database named `selfcure_pks`, and under *Networking* allow access
   from Azure services.
2. **Create the web app**: App Service, *Publish: Code*, *Runtime: PHP 8.3*, Linux.
3. **Startup command** (*Configuration → General settings*):
   `bash /home/site/wwwroot/azure/startup.sh`
   This serves the site from `public/`, runs migrations and the seeder, and
   caches the configuration on every start.
4. **Environment variables** (*Settings → Environment variables*): use
   `.env.example` as the list. Production values:

   | Variable | Value |
   | --- | --- |
   | `APP_ENV` / `APP_DEBUG` | `production` / `false` |
   | `APP_KEY` | output of `php artisan key:generate --show` |
   | `APP_URL` | `https://<your-app>.azurewebsites.net` |
   | `DB_HOST`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` | from the MySQL server |
   | `MYSQL_ATTR_SSL_CA` | `/etc/ssl/certs/ca-certificates.crt` |
   | `ADMIN_EMAIL` / `ADMIN_PASSWORD` | the first administrator login |
   | `SESSION_SECURE_COOKIE` | `true` |
   | `LOG_CHANNEL` | `stderr` |
   | `MAIL_*` | a real SMTP account (needed for 2FA codes and password resets) |
   | `GEMINI_API_KEY` | optional |

5. **Connect GitHub**: download the *publish profile* from the App Service
   overview page. In the GitHub repository, add it as the secret
   `AZURE_WEBAPP_PUBLISH_PROFILE`, and add the app name as the variable
   `AZURE_WEBAPP_NAME`. Every push to `main` then builds and deploys the site.

After the first deploy, sign in with `ADMIN_EMAIL` / `ADMIN_PASSWORD` (choose
the *Admin* tab on the login page) and change the password from the Profile page.

## Project structure

| Path | Contents |
| --- | --- |
| `app/Services` | Scoring (`MaturityScoringService`), action plan sync, AI insights, report data |
| `app/Http/Controllers` | SME and administrator pages, PDF reports |
| `database/migrations`, `database/seeders` | Schema; domains, questions and actions (EN + MS); administrator |
| `resources/views` | Blade templates, including `reports/` for the PDFs |
| `public/css/theme.css` | The full design system (SME and administrator themes) |
| `lang/ms.json` | Bahasa Melayu translations |
| `azure/` | nginx site and startup script for Azure App Service |

## Tests

```bash
php artisan test
```

Two default Laravel Breeze tests (login, registration) are known to fail
because registration and login require extra portal/business fields.
