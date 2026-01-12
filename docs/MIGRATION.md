# Migration Guide from V2 to V3

This guide helps you migrate from Neos Chronos V2 to V3.

## Before You Start

**⚠️ IMPORTANT**: Always backup your V2 database and files before migrating!

```bash
# Backup database
mysqldump -u user -p chronos_v2 > chronos_v2_backup_$(date +%Y%m%d).sql

# Backup files
tar -czf chronos_v2_files_$(date +%Y%m%d).tar.gz /path/to/chronos_v2/
```

## Key Differences

### Database Changes
- **New tables**: `chronos_player_awards`, `chronos_sessions`
- **Modified schema**: Updated character sets to utf8mb4
- **New columns**: Country code, IP address tracking
- **Security**: Admin passwords now use Argon2ID instead of MD5/SHA1

### PHP Changes
- **Minimum PHP**: 8.0+ (V2 supported PHP 5.x)
- **Database**: PDO with prepared statements (V2 used mysqli)
- **Security**: Enhanced CSRF protection, better input validation
- **Sessions**: Improved session handling

### Configuration Changes
- **Environment file**: Now uses `.env` for sensitive data
- **Constants**: Moved to `config.php` with better organization
- **Paths**: Absolute paths using constants

## Migration Steps

### 1. Install V3

Follow the [Installation Guide](INSTALLATION.md) to set up V3 in a new directory:

```bash
# Don't overwrite V2 - install in new directory
cd /var/www/html
mkdir chronos_v3
cd chronos_v3
# Extract V3 files here
```

### 2. Export V2 Data

Export your V2 player data:

```sql
-- Connect to V2 database
mysql -u user -p chronos_v2

-- Export players
SELECT * FROM players INTO OUTFILE '/tmp/v2_players.csv'
FIELDS TERMINATED BY ',' ENCLOSED BY '"'
LINES TERMINATED BY '\n';

-- Export weapons stats
SELECT * FROM weapons INTO OUTFILE '/tmp/v2_weapons.csv'
FIELDS TERMINATED BY ',' ENCLOSED BY '"'
LINES TERMINATED BY '\n';

-- Export maps stats
SELECT * FROM maps INTO OUTFILE '/tmp/v2_maps.csv'
FIELDS TERMINATED BY ',' ENCLOSED BY '"'
LINES TERMINATED BY '\n';
```

### 3. Transform Data

V3 has a different schema. Create a migration script:

```sql
-- Connect to V3 database
USE chronos_stats;

-- Import players (adjust field mapping as needed)
LOAD DATA INFILE '/tmp/v2_players.csv'
INTO TABLE chronos_players
FIELDS TERMINATED BY ',' ENCLOSED BY '"'
LINES TERMINATED BY '\n'
(player_name, player_guid, kills, deaths, headshots, @dummy, rating, play_time, games_played);

-- Update calculated fields
UPDATE chronos_players 
SET team_kills = 0 
WHERE team_kills IS NULL;

-- Recalculate ratings with new formula
UPDATE chronos_players 
SET rating = (kills * 1) + (deaths * -1) + (headshots * 2);
```

### 4. Update Admin Passwords

V2 passwords won't work in V3. Create new admin account:

```sql
-- Create new admin with secure password
INSERT INTO chronos_admin (username, email, password, is_active)
VALUES (
  'admin',
  'admin@yourdomain.com',
  '$argon2id$v=19$m=65536,t=4,p=1$...',  -- Generate with password_hash()
  1
);
```

Generate password hash:
```php
<?php
// Save as generate_hash.php
echo password_hash('your_new_password', PASSWORD_ARGON2ID);
```

### 5. Migrate Awards

Map V2 awards to V3:

```sql
-- V3 awards are in chronos_awards table
-- Create mapping based on your V2 awards
INSERT INTO chronos_player_awards (player_id, award_id, earned_date)
SELECT 
  p.id as player_id,
  a.id as award_id,
  NOW() as earned_date
FROM chronos_players p
INNER JOIN (
  -- Your V2 award conditions
  SELECT player_id FROM v2_awards WHERE award_type = 'sharpshooter'
) v2 ON p.player_guid = v2.player_guid
INNER JOIN chronos_awards a ON a.award_name = 'Sharpshooter';
```

### 6. Configure V3

Update `.env` with your V2 settings:

```env
# V2 server settings
SERVER_NAME="Your Old Server Name"
SERVER_IP=123.45.67.89
SERVER_PORT=3000

# Keep your V2 timezone
APP_TIMEZONE=America/New_York
```

### 7. Test Migration

1. **Verify player count**:
   ```sql
   -- Should match V2
   SELECT COUNT(*) FROM chronos_players;
   ```

2. **Check statistics**:
   ```sql
   SELECT SUM(kills), SUM(deaths) FROM chronos_players;
   ```

3. **Test website**: Navigate to V3 URL and check:
   - Player leaderboard
   - Weapon stats
   - Map stats
   - Admin panel login

### 8. Redirect Traffic

Once tested, redirect from V2 to V3:

**.htaccess in V2 directory**:
```apache
RewriteEngine On
RewriteRule ^(.*)$ /chronos_v3/$1 [R=301,L]
```

**Or update DNS/virtual host** to point to V3.

## Common Migration Issues

### Players Have Wrong Stats

**Problem**: Stats don't match V2

**Solution**: Verify field mapping in import:
```sql
-- Check a known player
SELECT * FROM chronos_players WHERE player_name = 'KnownPlayer';
```

### Admin Can't Login

**Problem**: V2 password doesn't work

**Solution**: V3 uses different hashing. Create new password:
```php
echo password_hash('newpassword', PASSWORD_ARGON2ID);
```

### Missing Awards

**Problem**: Awards didn't migrate

**Solution**: Awards system changed. Re-evaluate based on current stats:
```sql
-- Run award evaluation
-- Example: Sharpshooter award
INSERT INTO chronos_player_awards (player_id, award_id, earned_date)
SELECT id, (SELECT id FROM chronos_awards WHERE award_name = 'Sharpshooter'), NOW()
FROM chronos_players
WHERE headshots >= 100;
```

### Encoding Issues

**Problem**: Player names show weird characters

**Solution**: Convert to UTF-8:
```sql
ALTER TABLE chronos_players CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

## Feature Mapping

| V2 Feature | V3 Equivalent | Notes |
|------------|---------------|-------|
| player.php | index.php | Main leaderboard |
| weapons.php | modules/weapons/index.php | Weapon stats |
| maps.php | modules/maps/index.php | Map stats |
| awards.php | modules/awards/index.php | Awards display |
| admin.php | admin/index.php | Admin panel |
| stats_import.php | API upload | Now uses REST API |

## Data Validation Script

Run this to validate migration:

```sql
-- Validation queries
SELECT 'Total Players' as Metric, COUNT(*) as V3_Count FROM chronos_players
UNION ALL
SELECT 'Total Kills', SUM(kills) FROM chronos_players
UNION ALL
SELECT 'Total Deaths', SUM(deaths) FROM chronos_players
UNION ALL
SELECT 'Total Games', SUM(games_played) FROM chronos_players;

-- Compare with V2:
-- mysql -u user -p chronos_v2 -e "SELECT COUNT(*) FROM players"
```

## Post-Migration

1. **Test thoroughly** before decommissioning V2
2. **Keep V2 backup** for at least 30 days
3. **Update bookmarks** and links
4. **Notify players** of new URL
5. **Monitor logs** for issues

## Rollback Plan

If you need to rollback:

1. Stop V3 web server
2. Restore V2:
   ```bash
   mysql -u user -p chronos_v2 < chronos_v2_backup.sql
   ```
3. Redirect traffic back to V2

## Getting Help

Migration issues? Create a GitHub issue:
- Include V2 version number
- Database export sample
- Error messages

## Incremental Migration

For large databases, migrate in stages:

1. **Phase 1**: Players and basic stats
2. **Phase 2**: Weapons and maps
3. **Phase 3**: Awards and rankings
4. **Phase 4**: Historical/monthly data

This allows testing between phases.

## Success Checklist

- [ ] V2 database backed up
- [ ] V2 files backed up
- [ ] V3 installed and configured
- [ ] Players migrated and counted
- [ ] Statistics verified
- [ ] Weapons stats migrated
- [ ] Maps stats migrated
- [ ] Awards configured
- [ ] Admin access tested
- [ ] Website navigation tested
- [ ] Stats upload configured
- [ ] Traffic redirected
- [ ] Users notified

Congratulations on upgrading to V3! 🎉
