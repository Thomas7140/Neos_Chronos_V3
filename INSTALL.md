# Installation Guide - Chronos V3

This guide will help you install and configure **BAB.Stats Neos Chronos V3** on your server.

## Prerequisites

Before you begin, ensure you have:

- **PHP 8.0 or higher** with the following extensions:
  - PDO
  - pdo_mysql
  - mbstring
  - json (usually enabled by default)
- **MariaDB 11** or **MySQL 8+**
- **Web server** (Apache 2.4+ or Nginx)
- Command-line access to your server
- Database management access (phpMyAdmin or command-line)

## Step 1: Verify PHP Version and Extensions

Check your PHP version:

```bash
php -v
```

You should see PHP 8.0 or higher.

Check required extensions:

```bash
php -m | grep -E '(PDO|pdo_mysql|mbstring)'
```

All three should be listed. If not, install them:

**Ubuntu/Debian:**
```bash
sudo apt-get install php8.0-mysql php8.0-mbstring
```

**CentOS/RHEL:**
```bash
sudo yum install php80-pdo php80-mysqlnd php80-mbstring
```

## Step 2: Download Chronos V3

Clone the repository or download the latest release:

```bash
cd /var/www
git clone https://github.com/Thomas7140/Neos_Chronos_V3.git
cd Neos_Chronos_V3
```

Or download and extract the ZIP:

```bash
wget https://github.com/Thomas7140/Neos_Chronos_V3/archive/refs/heads/main.zip
unzip main.zip
mv Neos_Chronos_V3-main Neos_Chronos_V3
cd Neos_Chronos_V3
```

## Step 3: Configure Database

### 3.1 Create Database and User

Log in to MariaDB/MySQL:

```bash
mysql -u root -p
```

Create the database and user:

```sql
CREATE DATABASE chronos_stats CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'chronos_user'@'localhost' IDENTIFIED BY 'your_secure_password';
GRANT ALL PRIVILEGES ON chronos_stats.* TO 'chronos_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### 3.2 Import Database Schema

Import the schema file:

```bash
mysql -u chronos_user -p chronos_stats < database/schema.sql
```

Enter the password you created in step 3.1.

Verify the tables were created:

```bash
mysql -u chronos_user -p chronos_stats -e "SHOW TABLES;"
```

You should see: `players`, `weapons`, `servers`, `maps`, `rounds`, `events`

## Step 4: Configure Application

Copy the sample configuration file:

```bash
cp config/config.sample.php config/config.php
```

Edit the configuration file:

```bash
nano config/config.php
# or
vi config/config.php
```

Update the database settings:

```php
'database' => [
    'host' => 'localhost',
    'port' => 3306,
    'name' => 'chronos_stats',
    'user' => 'chronos_user',
    'pass' => 'your_secure_password',  // Change this!
    'charset' => 'utf8mb4',
    // ... rest of config
],
```

Also update the site settings:

```php
'site' => [
    'title' => 'Your Server Stats',  // Customize this
    'url' => 'http://yourdomain.com',  // Your actual URL
    'timezone' => 'America/New_York',  // Your timezone
    'items_per_page' => 50,
],
```

## Step 5: Set File Permissions

Set appropriate permissions:

```bash
# Make config directory readable only by web server
chmod 750 config/
chmod 640 config/config.php

# Ensure web server can read public files
chmod 755 public/
chmod 644 public/*.php
chmod 644 public/css/*.css

# Set ownership (adjust 'www-data' to your web server user)
chown -R www-data:www-data /var/www/Neos_Chronos_V3
```

## Step 6: Configure Web Server

### For Apache

Create a virtual host configuration:

```bash
sudo nano /etc/apache2/sites-available/chronos.conf
```

Add the following:

```apache
<VirtualHost *:80>
    ServerName stats.yourdomain.com
    DocumentRoot /var/www/Neos_Chronos_V3/public

    <Directory /var/www/Neos_Chronos_V3/public>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/chronos_error.log
    CustomLog ${APACHE_LOG_DIR}/chronos_access.log combined
</VirtualHost>
```

Enable the site and required modules:

```bash
sudo a2enmod rewrite
sudo a2ensite chronos.conf
sudo systemctl reload apache2
```

### For Nginx

Create a server block:

```bash
sudo nano /etc/nginx/sites-available/chronos
```

Add the following:

```nginx
server {
    listen 80;
    server_name stats.yourdomain.com;
    root /var/www/Neos_Chronos_V3/public;
    
    index index.php;
    
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    
    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/var/run/php/php8.0-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }
    
    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

Enable the site:

```bash
sudo ln -s /etc/nginx/sites-available/chronos /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

## Step 7: Test Installation

Visit your site in a browser:

```
http://stats.yourdomain.com
```

You should see the Chronos V3 interface. The pages will show "No data" messages until you start tracking statistics.

## Step 8: Enable HTTPS (Recommended)

Use Let's Encrypt for free SSL certificates:

```bash
sudo apt-get install certbot python3-certbot-apache
# or for Nginx:
sudo apt-get install certbot python3-certbot-nginx

# Generate certificate
sudo certbot --apache -d stats.yourdomain.com
# or for Nginx:
sudo certbot --nginx -d stats.yourdomain.com
```

## Step 9: Set Up Stat Tracking

To populate the database with game statistics, you need to set up a stat tracker that sends data to Chronos V3. This depends on your game server setup.

### API Endpoint (for trackers)

Trackers should send POST requests to:
```
http://stats.yourdomain.com/api/track.php
```

(Note: You may need to implement `api/track.php` based on your tracker's format)

## Troubleshooting

### Database Connection Errors

- Verify database credentials in `config/config.php`
- Ensure MariaDB/MySQL is running: `sudo systemctl status mariadb`
- Check database user has proper permissions
- Verify firewall allows database connections

### Page Not Found (404) Errors

- Check web server configuration
- Verify document root points to `public/` directory
- Ensure mod_rewrite (Apache) is enabled
- Check file permissions

### Blank Pages

- Check PHP error logs: `/var/log/apache2/error.log` or `/var/log/nginx/error.log`
- Verify PHP version is 8.0+
- Ensure all required PHP extensions are installed
- Check file permissions on config files

### Permission Denied Errors

- Verify web server user owns the files: `chown -R www-data:www-data /var/www/Neos_Chronos_V3`
- Check directory permissions: `755` for directories, `644` for files
- Ensure config directory is readable by web server

## Getting Help

If you encounter issues:

1. Check the error logs
2. Review this installation guide
3. Open an issue on GitHub with:
   - Your PHP version (`php -v`)
   - Your database version (`mysql --version`)
   - Error messages from logs
   - Steps to reproduce the issue

## Next Steps

- Customize the appearance by editing `public/css/style.css`
- Set up automated stat tracking
- Configure backups for your database
- Monitor system performance
- Join the community!

---

**Congratulations!** Chronos V3 is now installed and ready to track your game statistics.
