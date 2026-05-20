# Deploy RememberME no CapRover (MySQL)

## Prasyarat

1. Database MySQL sudah dibuat:

```sql
CREATE DATABASE rememberme CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

2. Portal CapRover: `http://202.150.156.39:9300`

## 1. Buat aplikasi di CapRover

1. Login ke dashboard CapRover
2. **Apps** → **Create New App** → nama: `rememberme`
3. **HTTP Settings** → **Container HTTP Port:** `80`
4. URL publik: `http://rememberme.202.150.156.39:9080`

## 2. Environment variables (App Configs)

Salin ke **App Configs** di CapRover (ganti `APP_KEY`):

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

Generate `APP_KEY` (lokal):

```bash
php artisan key:generate --show
```

**Catatan:** Password dashboard CapRover (`iaas_paas_@01`) berbeda dengan password MySQL (`iaas_paas@01`).

## 3. Deploy

### Opsi A — Tanpa Git (disarankan di Windows)

Dari folder `Final/`:

```powershell
npm install -g caprover
cd Final
.\deploy-caprover.ps1
```

Script membuat `deploy.tar.gz` lalu upload ke CapRover.

### Opsi B — Perintah manual (tarball)

```powershell
cd Final
tar -czf deploy.tar.gz --exclude=node_modules --exclude=vendor --exclude=.env .
caprover deploy -a rememberme -h http://202.150.156.39:9300 -t deploy.tar.gz
```

### Opsi C — Dengan Git

Jika Git sudah terpasang:

```powershell
cd Final
git init
git add .
git commit -m "deploy"
caprover deploy -a rememberme -h http://202.150.156.39:9300
```

### Opsi D — Dashboard

Upload `deploy.tar.gz` lewat **Deployment** → **Method 3: Deploy via tarball** di CapRover.

**Catatan:** Pesan *"You are not in a git root directory"* hanya peringatan, tetapi CLI sering **berhenti** jika Git tidak ada. Pakai Opsi A atau B.

Pastikan root deploy = folder yang berisi `captain-definition` dan `Dockerfile`.

## 4. Verifikasi

- [ ] `http://rememberme.202.150.156.39:9080` menampilkan SPA
- [ ] Register / Login berfungsi
- [ ] API `/api/login` merespons JSON
- [ ] Buat tugas & kategori tersimpan di MySQL

Cek **App Logs** jika 502 atau error migration.

## 5. Troubleshooting

| Masalah | Solusi |
|---------|--------|
| 502 Bad Gateway | Cek logs; pastikan port container = 80 |
| SQL connection refused | Pastikan `DB_HOST` benar; DBaaS reachable dari container |
| Blank / no assets | Rebuild image; pastikan stage `npm run build` sukses |
| APP_KEY missing | Set `APP_KEY` di App Configs lalu redeploy |

## Arsitektur container

- **Nginx** listen `:80` (syarat CapRover)
- **PHP-FPM** `:9000` internal
- **Entrypoint:** migrate + config/route/view cache saat start
