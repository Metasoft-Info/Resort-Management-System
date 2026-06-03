# Tufan Convention Resort - cPanel Deployment Guide

## Files Prepared for Upload

| File | Location | Purpose |
|------|----------|---------|
| `lakeview-deploy.zip` | ~/Desktop/javed/lake-view/ | Main Laravel project |
| `database_backup.sql` | ~/Desktop/javed/lake-view/ | Database with all data |
| `.env.production` | Inside the ZIP | Production environment config |

---

## Step-by-Step Deployment

### Step 1: Upload Files to cPanel

1. **Login to cPanel**: http://tufanconventionresort.com/cpanel
   - Username: `tufanconx`
   - Password: `S#hFI8&HAqPF`

2. **Go to File Manager** → Navigate to `/home/tufanconx`

3. **Create a folder** named `laravel` in `/home/tufanconx/`

4. **Upload** `lakeview-deploy.zip` to `/home/tufanconx/laravel/`

5. **Extract** the ZIP file

---

### Step 2: Move Public Files to public_html

1. Open **Terminal** in cPanel (or use SSH)

2. Run these commands:
```bash
# Backup existing public_html contents
cd /home/tufanconx
mv public_html public_html_backup

# Create new public_html pointing to Laravel's public folder
ln -s /home/tufanconx/laravel/public /home/tufanconx/public_html
```

**Alternative (without symlink):**
```bash
# Move public folder contents to public_html
cp -r /home/tufanconx/laravel/public/* /home/tufanconx/public_html/
```

If using the alternative method, **edit** `/home/tufanconx/public_html/index.php`:
```php
// Change these lines:
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';

// To:
require __DIR__.'/../laravel/vendor/autoload.php';
$app = require_once __DIR__.'/../laravel/bootstrap/app.php';
```

---

### Step 3: Import Database

1. **Go to phpMyAdmin** in cPanel

2. **Select database**: `tufanconx_tufanresort`

3. Click **Import** tab

4. **Choose file**: `database_backup.sql`

5. Click **Go** to import

---

### Step 4: Configure Environment

1. In File Manager, go to `/home/tufanconx/laravel/`

2. **Rename** `.env.production` to `.env`

3. Or create `.env` file with this content:

```env
APP_NAME="Tufan Convention Resort"
APP_ENV=production
APP_KEY=base64:XJnQVldnvQS81rpURKOq3cAHJA15rQ19GlRPGwijBYk=
APP_DEBUG=false
APP_URL=http://tufanconventionresort.com

LOG_CHANNEL=stack
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=tufanconx_tufanresort
DB_USERNAME=tufanconx_tufanresort
DB_PASSWORD=JavedMir41@

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=public
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120
```

---

### Step 5: Set Permissions

Run in cPanel Terminal or SSH:
```bash
cd /home/tufanconx/laravel

# Set folder permissions
chmod -R 755 storage
chmod -R 755 bootstrap/cache

# Create storage symlink
php artisan storage:link

# Clear caches
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
```

---

### Step 6: Create .htaccess (if needed)

In `/home/tufanconx/public_html/.htaccess`:
```apache
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    # Redirect Trailing Slashes If Not A Folder...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    # Send Requests To Front Controller...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
```

---

## Verification

After deployment, visit:
- **Homepage**: http://tufanconventionresort.com
- **Admin Panel**: http://tufanconventionresort.com/admin/login

---

## Troubleshooting

### 500 Internal Server Error
```bash
cd /home/tufanconx/laravel
chmod -R 775 storage
chmod -R 775 bootstrap/cache
```

### Images Not Showing
```bash
cd /home/tufanconx/laravel
rm -rf public/storage
php artisan storage:link
```

### Database Connection Error
- Verify database credentials in `.env`
- Make sure database user has all privileges

---

## Security Reminder

⚠️ **CHANGE ALL PASSWORDS AFTER DEPLOYMENT:**
1. cPanel password
2. Database password (update in `.env` too)
3. Admin user password in the application

---

## Support
If you face any issues, check Laravel logs at:
`/home/tufanconx/laravel/storage/logs/laravel.log`

---

## GitHub Auto Deploy (Production Branch)

This project now includes:

- GitHub Action workflow: `.github/workflows/deploy-production.yml`
- Server deploy script: `deploy/production_deploy.sh`

### Branch Strategy

1. Keep your ongoing work in `master` (or any feature branch).
2. Create PR from `master` -> `production`.
3. Merge into `production`.
4. GitHub Actions will auto-deploy to server.

### Required GitHub Repository Secrets

Go to: GitHub repository -> Settings -> Secrets and variables -> Actions

Create these secrets:

- `PROD_SSH_HOST` = your server host (example: `198.54.115.196`)
- `PROD_SSH_PORT` = SSH port (example: `22`)
- `PROD_SSH_USER` = SSH user (example: `tufanconx`)
- `PROD_SSH_PRIVATE_KEY` = private key content (full multi-line key)
- `PROD_SSH_PASSPHRASE` = private key passphrase (if key is encrypted)
- `PROD_APP_DIR` = Laravel root path (the folder that contains `artisan`)
- `PROD_PHP_BIN` = `php` (or full path if needed)
- `PROD_COMPOSER_BIN` = `composer` (or full path if needed)

Examples for `PROD_APP_DIR`:

- `/home/tufanconx/laravel`
- `/home/tufanconx/tufanconventionresort.com` (only if this folder contains `artisan`)

### What Runs Automatically On Each Production Deploy

1. Put app in maintenance mode
2. Fetch latest code from `origin/production`
3. Hard reset working tree to `origin/production`
4. Install production composer dependencies
5. Run database migrations (`php artisan migrate --force`)
6. Ensure storage symlink exists
7. Clear and rebuild Laravel caches
8. Restart queue workers (if running)
9. Restore app from maintenance mode

### Notes

- Ensure the server project folder has Git configured with origin pointing to your GitHub repo.
- Ensure the deploy user has permission to run composer, php artisan, and write to `storage` and `bootstrap/cache`.
- If the private key is passphrase-protected, use an unencrypted deploy key dedicated for CI/CD.
