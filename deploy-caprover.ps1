# Deploy RememberME ke CapRover tanpa Git (pakai tarball)
# Usage: .\deploy-caprover.ps1

$ErrorActionPreference = "Stop"
$AppName = "rememberme"
$CapRoverUrl = "http://202.150.156.39:9300"
$TarFile = "deploy.tar.gz"

Write-Host ">> Membuat arsip $TarFile ..." -ForegroundColor Cyan

if (Test-Path $TarFile) { Remove-Item $TarFile -Force }

# Pastikan captain-definition & Dockerfile ikut ter-pack
tar -czf $TarFile `
    --exclude=node_modules `
    --exclude=vendor `
    --exclude=.env `
    --exclude=.git `
    --exclude=storage/logs `
    --exclude=storage/framework/cache `
    --exclude=storage/framework/sessions `
    --exclude=storage/framework/views `
    --exclude=database/database.sqlite `
    --exclude=$TarFile `
    .

Write-Host ">> Deploy ke CapRover (app: $AppName) ..." -ForegroundColor Cyan
Write-Host "   Masukkan password dashboard CapRover jika diminta." -ForegroundColor Yellow

caprover deploy -a $AppName -h $CapRoverUrl -t $TarFile

if ($LASTEXITCODE -eq 0) {
    Write-Host ">> Deploy selesai. Buka: http://${AppName}.202.150.156.39:9080" -ForegroundColor Green
} else {
    Write-Host ">> Deploy gagal (exit $LASTEXITCODE). Cek log di dashboard CapRover." -ForegroundColor Red
}
