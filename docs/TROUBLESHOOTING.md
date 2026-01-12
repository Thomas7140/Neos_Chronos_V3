# Troubleshooting Guide

Common issues and solutions for Chronos V3.

## Installation Issues

### Database Connection Failed

**Problem**: "Database connection failed" error during installation.

**Solutions**:
1. Verify database credentials in `.env`:
   ```bash
   cat .env | grep DB_
   ```

2. Test database connection:
   ```bash
   mysql -h localhost -u chronos_user -p chronos_stats
   ```

3. Check if MySQL/MariaDB is running:
   ```bash
   systemctl status mysql
   # or
   systemctl status mariadb
   ```

4. Ensure database user has proper permissions:
   ```sql
   GRANT ALL PRIVILEGES ON chronos_stats.* TO 'chronos_user'@'localhost';
   FLUSH PRIVILEGES;
   ```

### PHP Version Error

**Problem**: "PHP version must be 8.0 or higher"

**Solution**:
1. Check current PHP version:
   ```bash
   php -v
   ```

2. Update PHP (Ubuntu/Debian):
   ```bash
   sudo apt update
   sudo apt install php8.1 php8.1-mysql php8.1-mbstring
   ```

3. Switch PHP version:
   ```bash
   sudo update-alternatives --config php
   ```

### Permission Denied Errors

**Problem**: Cannot write to cache/logs/uploads directories

**Solution**:
```bash
chmod 777 cache logs uploads backups tmp
# Or more secure:
chown -R www-data:www-data /path/to/chronos
chmod 755 cache logs uploads backups tmp
```

## Runtime Issues

### White Screen / Blank Page

**Problem**: Page loads blank with no content

**Solutions**:
1. Enable error display in `config.php`:
   ```php
   define('APP_DEBUG', true);
   ```

2. Check PHP error log:
   ```bash
   tail -f /var/log/php/error.log
   ```

3. Check web server error log:
   ```bash
   # Apache
   tail -f /var/log/apache2/error.log
   
   # Nginx
   tail -f /var/log/nginx/error.log
   ```

### Session Errors

**Problem**: "Session could not be started" or login doesn't work

**Solutions**:
1. Check PHP session directory:
   ```bash
   ls -la /var/lib/php/sessions
   ```

2. Ensure it's writable:
   ```bash
   chmod 1733 /var/lib/php/sessions
   ```

3. Check session settings in `php.ini`:
   ```ini
   session.save_path = "/var/lib/php/sessions"
   session.gc_maxlifetime = 7200
   ```

### Admin Login Issues

**Problem**: Cannot login to admin panel

**Solutions**:
1. Reset admin password via database:
   ```sql
   UPDATE chronos_admin 
   SET password = '$argon2id$v=19$m=65536,t=4,p=1$...' 
   WHERE username = 'admin';
   ```

2. Use the provided hash generator:
   ```php
   <?php
   echo password_hash('newpassword', PASSWORD_ARGON2ID);
   ```

3. Check if admin account is active:
   ```sql
   UPDATE chronos_admin SET is_active = 1 WHERE username = 'admin';
   ```

## Performance Issues

### Slow Page Loads

**Problem**: Pages take too long to load

**Solutions**:
1. Enable caching in `.env`:
   ```env
   ENABLE_CACHE=true
   ```

2. Add database indexes:
   ```sql
   CREATE INDEX idx_rating ON chronos_players(rating DESC);
   CREATE INDEX idx_kills ON chronos_players(kills DESC);
   ```

3. Optimize MySQL:
   ```sql
   OPTIMIZE TABLE chronos_players;
   OPTIMIZE TABLE chronos_stats;
   ```

4. Enable PHP OPcache in `php.ini`:
   ```ini
   opcache.enable=1
   opcache.memory_consumption=128
   ```

### High Memory Usage

**Problem**: PHP memory limit exceeded

**Solutions**:
1. Increase memory limit in `php.ini`:
   ```ini
   memory_limit = 256M
   ```

2. Or in `.htaccess`:
   ```apache
   php_value memory_limit 256M
   ```

3. Optimize queries to fetch less data

## Database Issues

### Too Many Connections

**Problem**: "Too many connections" error

**Solutions**:
1. Increase max connections in MySQL:
   ```sql
   SET GLOBAL max_connections = 200;
   ```

2. Make it permanent in `/etc/mysql/my.cnf`:
   ```ini
   [mysqld]
   max_connections = 200
   ```

3. Close unused connections in code

### Corrupted Tables

**Problem**: Database table is marked as crashed

**Solutions**:
```sql
REPAIR TABLE chronos_players;
REPAIR TABLE chronos_stats;

-- Or repair all tables
mysqlcheck --repair --all-databases -u root -p
```

## Stats Upload Issues

### Stats Not Updating

**Problem**: Game stats not appearing in Chronos

**Solutions**:
1. Check if server is registered:
   ```sql
   SELECT * FROM chronos_servers;
   ```

2. Verify API key is correct

3. Check upload script logs

4. Test manual upload:
   ```bash
   curl -X POST https://your-domain.com/api/upload.php \
     -H "X-API-Key: your_key" \
     -H "Content-Type: application/json" \
     -d '{"test": "data"}'
   ```

### Duplicate Player Entries

**Problem**: Same player appears multiple times

**Solutions**:
1. Ensure player GUID is unique and consistent

2. Merge duplicate entries:
   ```sql
   -- Backup first!
   UPDATE chronos_players SET kills = kills + (
     SELECT SUM(kills) FROM chronos_players WHERE player_guid = 'duplicate'
   ) WHERE id = (
     SELECT MIN(id) FROM chronos_players WHERE player_guid = 'duplicate'
   );
   ```

## Security Issues

### Suspected Hack Attempt

**Problem**: Suspicious activity in logs

**Solutions**:
1. Check access logs:
   ```bash
   grep "POST /admin" /var/log/apache2/access.log
   ```

2. Review Chronos logs:
   ```bash
   cat logs/chronos_*.log | grep WARNING
   ```

3. Change admin password immediately

4. Update `.htaccess` to block IP:
   ```apache
   <Limit GET POST>
     order allow,deny
     deny from 123.45.67.89
     allow from all
   </Limit>
   ```

### SQL Injection Attempt

**Problem**: Suspicious SQL in logs

**Solutions**:
1. Chronos uses prepared statements (protected by default)

2. Update to latest version

3. Review custom code for vulnerabilities

## Getting Help

If your issue isn't listed here:

1. **Enable Debug Mode**:
   ```env
   APP_DEBUG=true
   ```

2. **Check Logs**:
   - Application: `logs/chronos_*.log`
   - PHP: `/var/log/php/error.log`
   - Web server: `/var/log/apache2/` or `/var/log/nginx/`

3. **Create GitHub Issue**:
   - Include error messages
   - Include relevant log entries
   - Describe steps to reproduce
   - Include system info (PHP version, OS, etc.)

4. **Community Support**:
   - GitHub Discussions
   - BHD Community Forums

## Diagnostic Commands

Quick system check:
```bash
# PHP version
php -v

# PHP modules
php -m | grep -E "pdo|mysql|mbstring"

# MySQL status
systemctl status mysql

# Disk space
df -h

# Memory usage
free -m

# Check file permissions
ls -la cache/ logs/ uploads/
```

## Common Error Messages

### "Call to undefined function"
- Missing PHP extension
- Run: `php -m` to see installed modules
- Install missing extension: `apt install php8.1-{extension}`

### "Class 'PDO' not found"
- PDO not installed
- Install: `apt install php8.1-mysql`

### "Access denied for user"
- Wrong database credentials
- Check `.env` file
- Verify MySQL user exists

### "Headers already sent"
- Output before header() call
- Check for spaces/newlines before `<?php`
- Enable output buffering

## Still Having Issues?

Create a detailed bug report:

**Template**:
```
**Environment**:
- OS: Ubuntu 20.04
- PHP: 8.1.2
- MySQL: 8.0.28
- Web Server: Apache 2.4.41

**Issue**:
Describe the problem...

**Steps to Reproduce**:
1. Go to...
2. Click on...
3. See error...

**Error Messages**:
[Paste error log here]

**What I've Tried**:
- Solution 1
- Solution 2
```

Post to: https://github.com/Thomas7140/Neos_Chronos_V3/issues
