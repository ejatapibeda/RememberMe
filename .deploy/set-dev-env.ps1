$ErrorActionPreference = 'Stop'
$path = '.env'
if (-not (Test-Path $path)) { Copy-Item .env.example $path }
$content = Get-Content $path -Raw

function Upsert($text, $key, $value) {
    if ($text -match "(?m)^$key=.*$") {
        return [System.Text.RegularExpressions.Regex]::Replace($text, "(?m)^$key=.*$", "$key=$value")
    } else {
        if (-not $text.EndsWith("`n")) { $text += "`n" }
        return $text + "$key=$value`n"
    }
}

$content = Upsert $content 'APP_ENV' 'local'
$content = Upsert $content 'APP_DEBUG' 'true'
$content = Upsert $content 'LOG_CHANNEL' 'stack'
$content = Upsert $content 'LOG_LEVEL' 'debug'

Set-Content $path $content -NoNewline
Write-Host '.env switched to development (APP_ENV=local, APP_DEBUG=true)'
