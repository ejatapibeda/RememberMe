# Siapkan deploy CapRover (captain-definition saja, tanpa Dockerfile terpisah)
$ErrorActionPreference = "Stop"
$TarFile = "deploy.tar.gz"

Write-Host ">> Build assets Vue (wajib sebelum deploy)..." -ForegroundColor Cyan
if (-not (Test-Path "node_modules")) {
    npm install
}
npm run build
if (-not (Test-Path "public/build/manifest.json")) {
    Write-Host "ERROR: public/build/manifest.json tidak ada. Build gagal." -ForegroundColor Red
    exit 1
}

Write-Host ">> Membuat $TarFile ..." -ForegroundColor Cyan
if (Test-Path $TarFile) { Remove-Item $TarFile -Force }

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

$sizeMb = [math]::Round((Get-Item $TarFile).Length / 1MB, 2)
Write-Host ">> Selesai: $TarFile ($sizeMb MB)" -ForegroundColor Green
Write-Host ""
Write-Host "Deploy:" -ForegroundColor Yellow
Write-Host "  1. Dashboard: http://202.150.156.39:9300 -> Apps -> rememberme -> Deployment -> upload $TarFile"
Write-Host "  2. Atau: .\deploy-api.ps1 -AppToken TOKEN_ANDA"
