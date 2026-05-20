# Deploy RememberME di CapRover (Laravel + Vue)

Build menggunakan **`captain-definition`** saja (tanpa file `Dockerfile` terpisah). CapRover akan membangun image dari `dockerfileLines` di dalam file itu.

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
APP_KEY=base64:GANTI_DENGAN_KEY_BARU
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
```

Generate key lokal: `php artisan key:generate --show`

## 3. Deploy

### Langkah 1 — Build & buat tarball

```powershell
cd Final
.\deploy-caprover.ps1
```

Script ini:
- `npm run build` (Vue → `public/build/`)
- Membuat `deploy.tar.gz` (termasuk `captain-definition`, `docker/`, kode Laravel)

### Langkah 2 — Upload

**Dashboard (disarankan):**

1. `http://202.150.156.39:9300` → **Apps** → **rememberme** → **Deployment**
2. **Deploy via tarball** → upload `deploy.tar.gz`
3. Tunggu build selesai (lihat **App Logs**)

**Atau API (CLI caprover sering gagal untuk IP:9300):**

```powershell
.\deploy-api.ps1 -AppToken TOKEN_DARI_DASHBOARD
```

Jangan pakai `caprover deploy -h ...` — URL berubah ke `captain.202.150.156.39` dan gagal.

## 4. Isi `captain-definition`

File [`captain-definition`](captain-definition) berisi:

- PHP 8.3-FPM + Nginx di **port 80**
- `composer install --no-dev`
- Entrypoint: migrate, config cache, `storage:link`
- Menggunakan config di folder `docker/` (nginx, supervisord, entrypoint)

## 5. Verifikasi

- [ ] Build sukses di App Logs
- [ ] `http://rememberme.202.150.156.39:9080` tampil SPA
- [ ] Login / register / buat tugas berfungsi

## Troubleshooting

| Masalah | Solusi |
|---------|--------|
| Blank page, no JS | Jalankan `npm run build` lalu deploy ulang |
| 502 Bad Gateway | Port container harus **80** |
| DB error | Cek `DB_HOST`, buat DB `rememberme`, password MySQL `iaas_paas@01` |
| Build gagal composer | Pastikan `composer.lock` ikut di tarball |
