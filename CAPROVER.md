# Deploy RememberME di CapRover via GitHub (Laravel 12 + Vue 3)

CapRover meng-clone repo GitHub dan membangun image dari **`Dockerfile`** di root (ditunjuk oleh [`captain-definition`](captain-definition)). Dockerfile bersifat multi-stage dan **membangun aset Vue + menginstal dependensi sendiri**, jadi tidak perlu `npm run build` atau tarball manual.

## Prasyarat

1. Buat database (jika belum ada):

```sql
CREATE DATABASE rememberme CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

2. Portal: `http://202.150.156.39:9300`

## 1. Setup app di CapRover

| Setting | Nilai |
|---------|--------|
| App name | `rememberme` |
| Container HTTP Port | **80** |
| URL publik | `http://rememberme.202.150.156.39:9080` |

## 2. Environment variables (App Configs)

```env
APP_NAME=RememberME
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:bc/2sPgktNhu1X1ZC4Nam6ZXXU4gBdBojMrxKpdg2nk=
APP_URL=http://rememberme.202.150.156.39:9080
APP_TIMEZONE=Asia/Jakarta

DB_CONNECTION=mysql
DB_HOST=srv-captain--kwan-app-01
DB_PORT=3306
DB_DATABASE=rememberme
DB_USERNAME=root
DB_PASSWORD=iaas_paas@01

SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database

LOG_CHANNEL=stderr
LOG_LEVEL=error

VITE_API_URL=/api
RUN_MIGRATIONS=true

# Telegram Bot (isi token bot dari @BotFather)
TELEGRAM_BOT_TOKEN=
TELEGRAM_BOT_USERNAME=kwanremindbot
TELEGRAM_WEBHOOK_SECRET=
TELEGRAM_VERIFY_SSL=true
```

> `APP_KEY` di atas sudah di-generate untuk Anda. Boleh dipakai langsung atau ganti dengan `php artisan key:generate --show`.

## 3. Deploy via GitHub

### Langkah 1 — Push ke GitHub

```powershell
git add .
git commit -m "Production build untuk CapRover"
git push origin main
```

### Langkah 2 — Hubungkan repo di CapRover

1. `http://202.150.156.39:9300` → **Apps** → **rememberme** → **Deployment**
2. Bagian **Method 3: Deploy from Github/Bitbucket/Gitlab**
   - **Repository**: `github.com/<user>/<repo>`
   - **Branch**: `main`
   - **Username** + **Password/Token**: gunakan GitHub Personal Access Token (scope `repo`)
3. Klik **Save & Update**, lalu **Force Build**.
4. (Opsional) Aktifkan auto-deploy: salin **webhook URL** yang muncul ke GitHub repo → **Settings → Webhooks**. Setiap `git push` akan otomatis build ulang.
5. Pantau progres di **App Logs**.

## 4. Cara kerja build (`Dockerfile`)

[`captain-definition`](captain-definition) menunjuk ke `./Dockerfile` (multi-stage):

- **Stage `assets`** (`node:20`): `npm ci` + `npm run build` → `public/build/`
- **Stage `vendor`** (`composer:2`): `composer install --no-dev`
- **Stage `app`** (`php:8.3-fpm-alpine`): Nginx + PHP-FPM + Supervisor di **port 80**
- **Supervisor** menjalankan: `php-fpm`, `nginx`, **`schedule:work`** (reminder tugas/Telegram tiap menit), dan **`queue:work`**
- **Entrypoint** ([`docker/entrypoint.sh`](docker/entrypoint.sh)): buat DB jika belum ada, `migrate --force`, `storage:link`, lalu `config/route/view:cache`

## 5. Verifikasi

- [ ] Build sukses di App Logs
- [ ] `http://rememberme.202.150.156.39:9080` tampil SPA
- [ ] Login / register / buat tugas berfungsi
- [ ] Reminder Telegram terkirim (cek log `scheduler`)

## Troubleshooting

| Masalah | Solusi |
|---------|--------|
| Blank page, no JS | Cek log build stage `assets`; pastikan `package-lock.json` ter-commit |
| 502 Bad Gateway | Container HTTP Port harus **80** |
| DB error | Cek `DB_HOST`, buat DB `rememberme`, password MySQL benar |
| `APP_KEY` warning | Set `APP_KEY` di App Configs (lihat bawah) |
| Reminder tidak jalan | Cek program `scheduler` di App Logs (Supervisor) |

## Generate APP_KEY

Lokal: `php artisan key:generate --show`, atau pakai yang sudah di-generate:

```
APP_KEY=base64:bc/2sPgktNhu1X1ZC4Nam6ZXXU4gBdBojMrxKpdg2nk=
```

> Letakkan nilai ini di **App Configs** CapRover, **jangan** commit `.env` ke GitHub.
