$ErrorActionPreference = 'Stop'
$path = '.env'
if (-not (Test-Path $path)) {
    Copy-Item .env.example $path
}
$content = Get-Content $path -Raw
$secret = [guid]::NewGuid().ToString('N')

function Upsert($text, $key, $value) {
    if ($text -match "(?m)^$key=.*$") {
        return [System.Text.RegularExpressions.Regex]::Replace($text, "(?m)^$key=.*$", "$key=$value")
    } else {
        if (-not $text.EndsWith("`n")) { $text += "`n" }
        return $text + "$key=$value`n"
    }
}

$content = Upsert $content 'TELEGRAM_BOT_TOKEN' '8892585156:AAFWsCXXykh8kbUYcnj_LLVFBBGJjy5JWYk'
$content = Upsert $content 'TELEGRAM_BOT_USERNAME' 'kwanremindbot'
if ($content -notmatch '(?m)^TELEGRAM_WEBHOOK_SECRET=.+$') {
    $content = Upsert $content 'TELEGRAM_WEBHOOK_SECRET' $secret
}

Set-Content $path $content -NoNewline
Write-Host '.env updated with Telegram config'
