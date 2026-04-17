param(
    [string]$SourceDb = 'cvnsu_bd',
    [string]$TargetDb = 'cvnsu_vbd',
    [string]$MysqlExe = 'C:\xampp\mysql\bin\mysql.exe',
    [string]$MysqlUser = 'root',
    [string]$MysqlPassword = '',
    [string]$TemplatePath = (Join-Path $PSScriptRoot 'legacy_to_v2_template.sql')
)

$ErrorActionPreference = 'Stop'

if (-not (Test-Path $MysqlExe)) {
    throw "mysql.exe nao encontrado em $MysqlExe"
}

if (-not (Test-Path $TemplatePath)) {
    throw "Template SQL nao encontrado em $TemplatePath"
}

$sql = Get-Content -Path $TemplatePath -Raw
$sql = $sql.Replace('__SOURCE_DB__', $SourceDb)

$tempFile = Join-Path $env:TEMP ("legacy_to_v2_{0}.sql" -f ([guid]::NewGuid().ToString('N')))
Set-Content -Path $tempFile -Value $sql -Encoding UTF8

try {
    $args = @('-u', $MysqlUser, '-D', $TargetDb)
    if ($MysqlPassword -ne '') {
        $args += "-p$MysqlPassword"
    }

    Get-Content -Path $tempFile | & $MysqlExe @args
    if ($LASTEXITCODE -ne 0) {
        throw "A migracao terminou com codigo $LASTEXITCODE"
    }
}
finally {
    if (Test-Path $tempFile) {
        Remove-Item $tempFile -Force
    }
}