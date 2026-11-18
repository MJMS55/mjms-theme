$ErrorActionPreference = 'Stop'
$projectRoot = Split-Path -Parent $PSScriptRoot
$buildRoot = Join-Path $projectRoot 'build'
$stage = Join-Path $buildRoot 'mjms_theme'
New-Item -ItemType Directory -Force -Path $stage | Out-Null
$items = @('appinfo', 'lib', 'css', 'img', 'README.md', 'CHANGELOG.md', 'COPYING', 'composer.json')
foreach ($item in $items) {
    Copy-Item -LiteralPath (Join-Path $projectRoot $item) -Destination $stage -Recurse -Force
}
$archive = Join-Path $buildRoot 'mjms_theme-0.1.0.tar.gz'
& tar -czf $archive -C $buildRoot 'mjms_theme'
if ($LASTEXITCODE -ne 0) { throw 'Archive creation failed' }
Get-Item -LiteralPath $archive | Select-Object FullName, Length
