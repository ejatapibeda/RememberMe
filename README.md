# RememberME — Laravel + Vue (satu aplikasi)

Gabungan **BackEnd** + **FrontEnd** dengan Laravel Vite. UI mengikuti folder `FrontEnd/`.

## Instalasi

```bash
cd Final
composer install
copy .env.example .env
php artisan key:generate
# SQLite:
type nul > database\database.sqlite
php artisan migrate
npm install
```

## Development

Terminal 1:
```bash
php artisan serve
```

Terminal 2:
```bash
npm run dev
```

Buka: **http://127.0.0.1:8000**

## Build production

```bash
npm run build
php artisan serve
```

## Struktur

| Bagian | Lokasi |
|--------|--------|
| API | `routes/api.php` |
| Vue (dari FrontEnd) | `resources/js/` |
| Blade shell | `resources/views/welcome.blade.php` |

API: `/api` — sama origin dengan SPA (`VITE_API_URL=/api`).

## Deploy CapRover + MySQL

Panduan lengkap: **[CAPROVER.md](CAPROVER.md)**

- App: `rememberme`
- URL: `http://rememberme.202.150.156.39:9080`
- Container port: **80**
- DB host: `srv-captain--kwan-app-01`

## Rebuild dari sumber

Jika `BackEnd/` atau `FrontEnd/` berubah, salin ulang ke `Final/` lalu terapkan konfigurasi Laravel Vite (lihat commit integrasi atau README root).
