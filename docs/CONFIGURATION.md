# Configuration Guide

This guide explains how to configure Chronos V3 to suit your needs.

## Environment Configuration

The `.env` file contains all environment-specific settings.

### Database Settings

```env
DB_HOST=localhost          # Database server hostname
DB_PORT=3306              # MySQL/MariaDB port
DB_NAME=chronos_stats     # Database name
DB_USER=your_username     # Database username
DB_PASS=your_password     # Database password
DB_PREFIX=chronos_        # Table prefix
```

### Application Settings

```env
APP_NAME="Chronos V3"     # Application name displayed on pages
APP_URL=http://localhost  # Your website URL
APP_DEBUG=false           # Enable debug mode (true/false)
APP_TIMEZONE=UTC          # Server timezone
```

### Security Settings

```env
SESSION_LIFETIME=7200     # Session lifetime in seconds (2 hours)
HASH_ALGORITHM=sha256     # Hash algorithm for security
CSRF_TOKEN_EXPIRY=3600    # CSRF token expiry in seconds
```

### Statistics Settings

```env
STATS_PER_PAGE=30         # Number of players per page
RECORDS_PER_PAGE=50       # Number of records per page
ENABLE_MONTHLY_STATS=true # Enable monthly statistics
ENABLE_HALL_OF_FAME=true  # Enable hall of fame
```

### Server Settings

```env
SERVER_NAME="My BHD Server"  # Your game server name
SERVER_IP=127.0.0.1          # Game server IP
SERVER_PORT=3000             # Game server port
```

## Advanced Configuration

Edit `config.php` for advanced settings.

### Rating System

Customize the rating calculation:

```php
// Rating calculation constants
define('RATING_KILL_POINTS', 1);        // Points per kill
define('RATING_DEATH_POINTS', -1);      // Points per death
define('RATING_HEADSHOT_BONUS', 2);     // Bonus for headshot
define('RATING_TEAMKILL_PENALTY', -5);  // Penalty for team kill
```

### Rank Thresholds

Ranks are defined in the database. To modify:

```sql
UPDATE chronos_ranks SET min_rating = 500 WHERE rank_name = 'Sergeant';
```

Or add new ranks:

```sql
INSERT INTO chronos_ranks (rank_name, min_rating, rank_icon, rank_order) 
VALUES ('Master Sergeant', 1500, 'rank_ms.png', 5);
```

### Game Types

Add or modify game types in `config.php`:

```php
define('GAME_TYPE_DM', 'Deathmatch');
define('GAME_TYPE_TDM', 'Team Deathmatch');
define('GAME_TYPE_CTF', 'Capture the Flag');
// Add more as needed
```

## Database Configuration

### Table Prefix

To change the table prefix, update in `.env`:

```env
DB_PREFIX=custom_prefix_
```

Then update all references in `config.php`.

### Connection Pooling

For high-traffic sites, configure connection pooling in your database server.

MySQL configuration (`/etc/mysql/my.cnf`):

```ini
[mysqld]
max_connections = 200
thread_cache_size = 8
query_cache_size = 64M
```

## Web Server Configuration

### Apache

`.htaccess` configuration:

```apache
# Enable compression
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/html text/plain text/xml text/css text/javascript application/javascript
</IfModule>

# Browser caching
<IfModule mod_expires.c>
    ExpiresActive On
    ExpiresByType image/jpg "access plus 1 year"
    ExpiresByType image/jpeg "access plus 1 year"
    ExpiresByType image/png "access plus 1 year"
    ExpiresByType text/css "access plus 1 month"
    ExpiresByType application/javascript "access plus 1 month"
</IfModule>

# Security headers
Header set X-Content-Type-Options "nosniff"
Header set X-Frame-Options "SAMEORIGIN"
Header set X-XSS-Protection "1; mode=block"
Header set Referrer-Policy "strict-origin-when-cross-origin"
```

### Nginx

Example configuration:

```nginx
server {
    # Gzip compression
    gzip on;
    gzip_types text/plain text/css application/json application/javascript text/xml;
    
    # Cache static files
    location ~* \.(jpg|jpeg|png|gif|ico|css|js)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }
    
    # Security headers
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-XSS-Protection "1; mode=block" always;
}
```

## Customization

### Themes

To customize the appearance, edit `templates/style.css`:

```css
:root {
    --primary-color: #2c3e50;      /* Main color */
    --secondary-color: #34495e;     /* Secondary color */
    --accent-color: #3498db;        /* Accent color */
}
```

### Templates

Templates are located in `templates/` directory. Create custom templates by copying and modifying existing ones.

### Awards

Configure awards in the database:

```sql
INSERT INTO chronos_awards (award_name, award_description, award_type, award_criteria)
VALUES ('Custom Award', 'Your description', 'kills', 'kills >= 500');
```

## Performance Optimization

### Enable Caching

In `.env`:

```env
ENABLE_CACHE=true
CACHE_LIFETIME=3600  # 1 hour
```

### Database Indexing

Add indexes for frequently queried columns:

```sql
CREATE INDEX idx_player_rating ON chronos_players(rating DESC);
CREATE INDEX idx_last_seen ON chronos_players(last_seen DESC);
```

### PHP Optimization

In `php.ini`:

```ini
memory_limit = 256M
max_execution_time = 60
opcache.enable = 1
opcache.memory_consumption = 128
```

## Backup Configuration

### Automated Database Backups

Create a cron job for daily backups:

```bash
0 2 * * * mysqldump -u chronos_user -p'password' chronos_stats > /backups/chronos_$(date +\%Y\%m\%d).sql
```

### Backup Rotation

Keep last 30 days:

```bash
find /backups -name "chronos_*.sql" -mtime +30 -delete
```

## Security Hardening

### File Permissions

```bash
# Application files
find . -type f -exec chmod 644 {} \;
find . -type d -exec chmod 755 {} \;

# Writable directories
chmod 777 cache logs uploads backups tmp
```

### Disable PHP Functions

In `php.ini`:

```ini
disable_functions = exec,passthru,shell_exec,system,proc_open,popen
```

### SSL/TLS Configuration

Force HTTPS in `.htaccess`:

```apache
RewriteEngine On
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
```

## Logging

Configure logging in `config.php`. Logs are stored in `logs/` directory.

View logs:

```bash
tail -f logs/chronos_$(date +%Y-%m-%d).log
```

## Troubleshooting

Enable debug mode to see detailed errors:

```env
APP_DEBUG=true
```

**Remember to disable debug mode in production!**
