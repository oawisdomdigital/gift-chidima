## Deployment Instructions

### First Time Setup
1. Edit `deploy-assets.ps1` and update these values:
   - `$ftpHost`: Your InfinityFree FTP hostname (usually ftpupload.net)
   - `$ftpUser`: Your InfinityFree FTP username (starts with epiz_)
   - `$remotePath`: Update if your path is different from /htdocs/assets/index.css

### Before Deployment
1. Build your frontend assets:
   ```powershell
   cd frontend
   npm run build
   ```

2. Run the deployment script:
   ```powershell
   .\deploy-assets.ps1
   ```
   - You'll be prompted for your FTP password
   - The script will copy the compiled CSS to your InfinityFree hosting

### Manual Deployment Steps (if script fails)
1. Build your frontend:
   ```powershell
   cd frontend
   npm run build
   ```

2. Using your FTP client:
   - Connect to your InfinityFree FTP server
   - Create an 'assets' folder in your root directory if it doesn't exist
   - Upload `frontend/dist/index.css` to `/assets/index.css`

### Verification
1. After deployment, check that your admin pages load correctly
2. Verify Tailwind styles are working in production
3. If styles aren't loading, check:
   - File permissions on the server (should be 644)
   - File path in the browser dev tools
   - Your site's directory structure matches the expected paths

### Rollback
If needed, you can rollback by:
1. Keeping a backup of your previous CSS file
2. Uploading the backup file if issues occur
3. Or temporarily switching to the CDN version by editing head.php