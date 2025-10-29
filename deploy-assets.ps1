# Deployment script for copying assets to production
$ErrorActionPreference = "Stop"

# Configuration
$sourceFile = "frontend\dist\assets\index-DFf-Pp12.css"
$ftpHost = "ftpupload.net"
$ftpUser = "if0_40258130"
$ftpPass = "Alid91075800"
$ftpPort = 21
$remotePath = "/htdocs/assets/index-DFf-Pp12.css"

# Verify source file exists
if (-not (Test-Path $sourceFile)) {
    Write-Error "Source file not found: $sourceFile"
    Write-Host "Please run 'npm run build' in the frontend directory first."
    exit 1
}

# Get FTP credentials if not provided
if (-not $ftpPass) {
    $securePass = Read-Host "Enter your FTP password" -AsSecureString
    $BSTR = [System.Runtime.InteropServices.Marshal]::SecureStringToBSTR($securePass)
    $ftpPass = [System.Runtime.InteropServices.Marshal]::PtrToStringAuto($BSTR)
}

try {
    Write-Host "Starting file upload to InfinityFree..."
    
    # Create FTP request
    $ftpUrl = "ftp://$ftpHost$remotePath"
    $webClient = New-Object System.Net.WebClient
    $webClient.Credentials = New-Object System.Net.NetworkCredential($ftpUser, $ftpPass)
    
    # Upload file
    $webClient.UploadFile($ftpUrl, $sourceFile)
    
    Write-Host "Upload completed successfully!" -ForegroundColor Green
    Write-Host "Tailwind CSS has been deployed to: $ftpUrl"
}
catch {
    Write-Error "Error uploading file: $_"
    exit 1
}
finally {
    if ($webClient) {
        $webClient.Dispose()
    }
}