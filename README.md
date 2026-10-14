# Compyvila

A Laravel 13 crypto editorial publication with Blade views, Eloquent models, MySQL, and plain CSS/JavaScript. Market information is manually maintained: no external market APIs, widgets, fonts, or analytics. There are no author profiles or About page.

## Local setup

Requires PHP 8.3+, Composer, MySQL/MariaDB, and PHP extensions required by Laravel, including PDO MySQL, fileinfo, and image support. The example environment targets the local XAMPP `compyvila` database.

```sh
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan compyvila:create-admin your@email.com
php artisan serve --host=127.0.0.1 --port=8080
```

For this migrated checkout, `.env`, its application key, the database, and the existing admin account are already configured. **Do not replace your existing `.env` or regenerate its key unnecessarily.** Open http://127.0.0.1:8080 and sign in at `/admin`. Existing login details remain in ignored `storage/admin-access.txt`. Save them in a password manager; change the password in Settings. The Artisan command generates a random password for a new email and never overwrites an existing account.

Set standard Laravel `DB_CONNECTION`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`, and optional `DB_SOCKET` for another database. Remove the XAMPP socket setting for a remote MySQL server. Sessions and cache use local files; no sessions/cache tables or queue workers are required.

## Application structure

- `routes/web.php`: named public and protected editorial routes.
- `app/Http/Controllers/PublicationController.php`: homepage, sections, articles, calendar, media kit, and sitemap.
- `app/Http/Controllers/Admin/`: authentication, dashboard, articles, events, snapshots, settings, subscriber export, and deletion.
- `app/Models/`: Eloquent publication records and the authenticatable `Admin` model.
- `resources/views/`: Blade layouts, pages, reusable components, and admin forms. Views contain no database queries.
- `database/migrations/`: a non-destructive baseline that adopts existing publication tables and creates missing ones.
- `database/seeders/DatabaseSeeder.php`: a repeatable sample-edition seeder that preserves existing content.
- `public/assets/`: existing responsive design and browser-local interactions; no asset build step is needed.
- `public/uploads/`: validated charts and illustrations, managed by Laravel Storage's `editorial` disk.

The standalone PHP router, authentication, PDO helpers, installer, and CLI admin script have been replaced with Laravel's request lifecycle, middleware, Auth session guard, validation, Eloquent, migrations, and Artisan commands. `public/router.php` only forwards local development requests into Laravel's standard entry point.

## Features

- Homepage with a latest-10 published blog hero slider (seven-second autoplay, pause, arrows, dots, swipe and keyboard controls, reduced-motion support), daily brief, timestamped manual prices, lead story, dispatches, analysis, events, and educational guides.
- News, Analysis, Bitcoin, Ethereum, Altcoins, and Learn; search, category filters, pagination, and sourced articles.
- Browser-local saved reading list, article sharing, and responsive mobile navigation.
- Manually curated calendar with filters, source links, and verification dates.
- Media kit with three partnership formats, SVG branding downloads, a downloadable standalone HTML kit printable to PDF, and an enquiry form.
- Contact, editorial policy, privacy, terms, market-source notes, and sitemap.
- Protected admin with drafts and scheduled publication, featured stories, chart uploads, prices, events, briefing/settings, subscriber CSV export, enquiries, deletion, and password changes.

## Preserved database and sample edition

The baseline migration adopts existing tables **without dropping, truncating, or rewriting their records**. Current articles, snapshots, events, settings, subscriptions, enquiries, and admin password hashes remain compatible. The existing legacy `login_attempts` table is no longer used; Laravel's cache-backed rate limiter handles login throttling.

The baseline migration intentionally refuses rollback because it cannot safely delete adopted tables. Use a reviewed migration for future changes and a database backup for restoration. `migrate:fresh` is destructive and is used only against isolated test databases by the test framework.

A fresh installation seeds ten educational sample articles, four illustrative prices, three sample editorial events, and a briefing. The visible preview label stays enabled until you replace those records and set a real contact email.

## Editorial workflow

1. Write or edit articles from the editorial desk. Body text uses blank lines for paragraphs and `## ` for headings. Raw HTML is escaped. Published articles require complete HTTP/HTTPS source links, one per line.
2. To schedule publication, choose Published and a future time. Public Eloquent queries expose it only after that timestamp; no external scheduler is required. All editorial times are IST.
3. Upload PNG/JPEG/WebP charts up to 5 MB and 10,000 pixels per side. Images get generated filenames, and replaced/deleted article images are cleaned up.
4. Update market observations manually with actual capture times and source URLs. They remain labelled as snapshots, never live quotes.
5. Set a real media/contact email, replace sample content, review policies, and disable the preview notice in Settings.

**Newsletter email delivery and enquiry notification emails are not configured.** Signups are stored for manual CSV export. Enquiries appear in the admin inbox; reply using your email client. Contact requests for removal can be fulfilled by deleting the relevant record.

The media kit does not invent audience numbers, performance figures, or rates. Verified reach and commercial availability are on request.

## Security and hosting

Laravel handles hashed passwords, encrypted sessions, session regeneration/expiry, CSRF tokens, input validation, login throttling, and prepared database queries. Protected routes use editorial middleware and an `admin` Auth guard. Forms retain validation errors without flashing passwords. Response middleware sets CSP and other security headers; admin responses are private and excluded from indexing.

Deploy to a PHP/Laravel-capable host, set its document root to **`public/`**, use HTTPS, set `APP_ENV=production`, `APP_DEBUG=false`, and the real `APP_URL`. Configure a restricted database user. Keep `.env`, application source, private `storage/`, and `vendor/` outside the document root. Allow writes to `storage/`, `bootstrap/cache/`, and `public/uploads/`; prevent the web server from executing uploaded files. Back up the database and uploads. Run `composer install --no-dev --optimize-autoloader`, `php artisan migrate --force`, and Laravel's configuration/route/view cache commands during a reviewed deployment. No hosted deployment has been performed in this task.

## Verification

```sh
php artisan test
python3 tests/integration.py
node --check public/assets/app.js
node tests/browser-check.mjs
```

Laravel feature tests use an in-memory SQLite database. They verify rendering, non-destructive schema adoption, old password compatibility, authentication, publishing, source validation, escaping, image cleanup, newsletters, CSV export, enquiries, manual updates, settings, and session expiry.

HTTP integration checks use another temporary SQLite database, isolated session/cache directories, and a temporary server. They exercise real CSRF checks, login throttling, multipart uploads, forms, and redirects without changing the MySQL publication.

The optional standalone Chrome check requires the local preview to be running. `CHROME_BIN` overrides the executable path. It tests desktop/tablet/mobile widths, navigation, and saved stories, and stores screenshots under ignored `storage/`. With `--admin`, it also uses the ignored local bootstrap login to verify the migrated admin interface without printing credentials.
