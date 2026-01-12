# Installation Guide

This guide will walk you through installing Chronos V3 on your web server.

## Prerequisites

Before you begin, ensure you have:

- Web server (Apache or Nginx)
- PHP 8.0 or higher
- MySQL 5.7+ or MariaDB 10.3+
- Access to your server via FTP/SFTP or SSH
- A Delta Force: Black Hawk Down or Team Sabre game server

## Step 1: Download and Extract

1. Download the latest release from GitHub
2. Extract the files to your web server directory
   ```bash
   cd /var/www/html
   unzip Neos_Chronos_V3.zip
   cd Neos_Chronos_V3
   ```

## Step 2: Configure Environment

1. Copy the example environment file:
   ```bash
   cp .env.example .env
   ```

2. Edit `.env` with your database credentials:
   ```bash
   nano .env
   ```

3. Update the following values:
   ```env
   DB_HOST=localhost
   DB_NAME=chronos_stats
   DB_USER=your_username
   DB_PASS=your_password
   
   APP_URL=http://your-domain.com
   ADMIN_EMAIL=admin@your-domain.com
   
   SERVER_NAME="Your BHD Server Name"
   ```

## Step 3: Create Database

1. Login to MySQL:
   ```bash
   mysql -u root -p
   ```

2. Create database:
   ```sql
   CREATE DATABASE chronos_stats CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   GRANT ALL PRIVILEGES ON chronos_stats.* TO 'chronos_user'@'localhost' IDENTIFIED BY 'secure_password';
   FLUSH PRIVILEGES;
   EXIT;
   ```

## Step 4: Set Permissions

Ensure the following directories are writable by the web server:

```bash
chmod 755 /var/www/html/Neos_Chronos_V3
chmod 777 cache logs uploads backups tmp
```

For better security, set ownership to the web server user:
```bash
chown -R www-data:www-data /var/www/html/Neos_Chronos_V3
```

## Step 5: Run Web Installer

1. Navigate to the installer in your web browser:
   ```
   http://your-domain.com/install.php
   ```

2. Follow the installation wizard:
   - **Step 1**: System requirements check
   - **Step 2**: Database schema installation
   - **Step 3**: Create admin account
   - **Step 4**: Complete installation

3. After installation, **delete or rename** `install.php` for security:
   ```bash
   rm install.php
   ```

## Step 6: Configure Web Server

### Apache

Create or edit `.htaccess`:

```apache
# Enable rewrite engine
RewriteEngine On

# Security headers
Header set X-Content-Type-Options "nosniff"
Header set X-Frame-Options "SAMEORIGIN"
Header set X-XSS-Protection "1; mode=block"

# Prevent directory listing
Options -Indexes

# Protect sensitive files
<FilesMatch "^\.env">
    Order allow,deny
    Deny from all
</FilesMatch>
```

### Nginx

Add to your server block:

```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /var/www/html/Neos_Chronos_V3;
    index index.php index.html;

    # Security headers
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-XSS-Protection "1; mode=block" always;

    # Protect sensitive files
    location ~ /\.env {
        deny all;
    }

    # PHP processing
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.0-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

## Step 7: Configure Game Server

Configure your BHD/BHDTS game server to send stats to Chronos V3.

1. Set up the stats uploader on your game server
2. Point it to your Chronos installation URL
3. Configure the upload interval (recommended: every 5-10 minutes)

## Step 8: Verify Installation

1. Visit your stats page:
   ```
   http://your-domain.com/
   ```

2. Login to admin panel:
   ```
   http://your-domain.com/admin/login.php
   ```

3. Use the credentials you created during installation

## Troubleshooting

### Database Connection Error

- Verify `.env` file has correct credentials
- Check MySQL/MariaDB is running
- Ensure database user has proper permissions

### Permission Errors

```bash
chmod -R 755 /var/www/html/Neos_Chronos_V3
chmod -R 777 cache logs uploads backups tmp
```

### PHP Version Issues

Check PHP version:
```bash
php -v
```

Must be 8.0 or higher.

### Can't Access Admin Panel

- Clear browser cache and cookies
- Check session settings in `config.php`
- Verify PHP session directory is writable

## Security Recommendations

1. **Change default admin password immediately**
2. **Enable HTTPS** using Let's Encrypt:
   ```bash
   certbot --nginx -d your-domain.com
   ```

3. **Set APP_DEBUG to false** in production:
   ```env
   APP_DEBUG=false
   ```

4. **Restrict admin access** to specific IPs if possible

5. **Regular backups** of database:
   ```bash
   mysqldump -u chronos_user -p chronos_stats > backup.sql
   ```

## Next Steps

- Configure monthly stats reset (optional)
- Customize rating formulas in `config.php`
- Add custom awards and ranks
- Set up automated database backups

## Support

- GitHub Issues: https://github.com/Thomas7140/Neos_Chronos_V3/issues
- Documentation: See `docs/` folder
- Community Forums: Black Hawk Down community sites
