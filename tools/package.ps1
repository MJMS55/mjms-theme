$ErrorActionPreference = 'Stop'
$projectRoot = Split-Path -Parent $PSScriptRoot
$buildRoot = Join-Path $projectRoot 'build'
[xml]$manifest = Get-Content -LiteralPath (Join-Path $projectRoot 'appinfo/info.xml') -Raw
$version = [string]$manifest.info.version
$stagingRoot = Join-Path $buildRoot ([guid]::NewGuid().ToString())
$stage = Join-Path $stagingRoot 'mjms_theme'
New-Item -ItemType Directory -Force -Path (Join-Path $stage 'img') | Out-Null
try {
    $items = @('appinfo', 'lib', 'css', 'README.md', 'CHANGELOG.md', 'COPYING', 'composer.json')
    foreach ($item in $items) {
        Copy-Item -LiteralPath (Join-Path $projectRoot $item) -Destination $stage -Recurse
    }
    foreach ($item in @('img/app.svg', 'img/icons')) {
        Copy-Item -LiteralPath (Join-Path $projectRoot $item) -Destination (Join-Path $stage 'img') -Recurse
    }
    $archive = Join-Path $buildRoot ('mjms_theme-' + $version + '.tar.gz')
    & tar -czf $archive -C $stagingRoot 'mjms_theme'
    if ($LASTEXITCODE -ne 0) { throw 'Archive creation failed' }
    $zipArchive = Join-Path $buildRoot ('mjms_theme-' + $version + '.zip')
    Compress-Archive -LiteralPath $stage -DestinationPath $zipArchive -Force
    Get-Item -LiteralPath $archive, $zipArchive | Select-Object FullName, Length
} finally {
    $resolved = (Resolve-Path -LiteralPath $stagingRoot).Path
    if ((Split-Path -Parent $resolved) -eq (Resolve-Path -LiteralPath $buildRoot).Path) {
        Remove-Item -LiteralPath $resolved -Recurse -Force
    }
}
