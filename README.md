# Kniploket Tiko (Laravel)

Laravel webapplicatie voor User Story 01 (Klant Read) en User Story 02 (Klant Update).

## Vereisten

- PHP 8.2+
- Composer
- MySQL 8+
- Node.js (voor Vite assets)

## Installatie

### 1. Database

**Met testdata:**
```bash
mysql -u root -p < database/scripts/create_kniploket_tiko.sql
mysql -u root -p < database/scripts/stored_procedures.sql
```

**Lege database (geen klanten/producten):**
```bash
mysql -u root -p < database/scripts/create_kniploket_tiko_empty.sql
mysql -u root -p < database/scripts/stored_procedures_empty.sql
```

### 2. Laravel

```bash
composer install
cp .env.example .env   # indien nodig
php artisan key:generate
```

Pas `.env` aan (lege database staat hier):

```env
# DB_USE_EMPTY=false  →  kniploket_tiko         (met testdata)
# DB_USE_EMPTY=true   →  kniploket_tiko_empty   (lege tabellen, geen klanten)
DB_USE_EMPTY=false
DB_DATABASE=kniploket_tiko
DB_DATABASE_EMPTY=kniploket_tiko_empty
DB_USERNAME=root
DB_PASSWORD=
```

Na wijziging van `DB_USE_EMPTY`: `php artisan config:clear`

### 3. Frontend assets

```bash
npm install
npm run build
```

### 4. Starten

```bash
php artisan serve
```

Open http://localhost:8000 — je wordt doorgestuurd naar `/login`.

### Login (testaccount)

| E-mail | Wachtwoord | Rol |
|--------|------------|-----|
| eigenaar@kniplokettiko.nl | password | eigenaar (volledige toegang) |
| fatima@kniplokettiko.nl | password | medewerker |
| piet.van.loenen@gmail.com | password | klant |

Alleen **eigenaar** heeft toegang tot klantenbeheer (`/klanten`).

## Laravel MVC-structuur

```
app/
  Http/
    Controllers/
      HomeController.php
      KlantController.php
    Requests/
      KlantPostcodeSearchRequest.php   # Server validatie
      UpdateKlantRequest.php
  Repositories/
    KlantRepository.php                # Stored procedures
  Services/
    KlantFormatter.php
config/kniploket.php
database/scripts/
  create_kniploket_tiko.sql
  stored_procedures.sql
resources/views/
  layouts/app.blade.php
  home/index.blade.php
  klanten/index.blade.php, show.blade.php, edit.blade.php
routes/web.php
```

## Routes

| URL | Route name | Actie |
|-----|------------|-------|
| `/` | home | Homepagina |
| `/klanten` | klanten.index | Overzicht + postcode filter |
| `/klanten/{id}` | klanten.show | Detail |
| `/klanten/{id}/wijzigen` | klanten.edit | Wijzigformulier |
| PUT `/klanten/{id}` | klanten.update | Opslaan |

## Programmeerrichtlijnen

| # | Onderwerp | Laravel implementatie |
|---|-----------|----------------------|
| 1 | Commentaar | PHPDoc op classes/methodes |
| 2 | Joins | INNER JOIN in stored procedures |
| 3 | Try Catch | Controllers + Repository |
| 4 | PSR-12 | Namespaces, type hints, strict types |
| 5 | Stored Procedures | `sp_Klant_*` via `KlantRepository` |
| 6 | Naamgeving | Beschrijvende methoden en variabelen |
| 7 | MVC | Laravel Controllers, Views, Repository |
| 8 | Security | CSRF (`@csrf`), XSS (`{{ }}`), prepared statements |
| 9 | Validatie | Form Requests + `validation.js` |
| 10 | Technische log | `Log::` → `storage/logs/laravel.log` |
| 11 | Meldingen | Session flash (`success` / `error`) |
