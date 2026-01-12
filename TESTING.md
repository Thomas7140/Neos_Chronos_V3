# Testing Guide - Chronos V3

This document describes how to test the Chronos V3 system to ensure PHP 8 and MariaDB 11 compatibility.

## Automated Testing

Run the automated test script:

```bash
./test.sh
```

This script tests:
- ✓ PHP 8+ version detection
- ✓ Required PHP extensions (PDO, pdo_mysql, mbstring, json)
- ✓ PHP syntax validation for all files
- ✓ File structure validation
- ✓ Class loading capabilities
- ✓ SQL schema compatibility

## Manual Testing

### 1. PHP Version and Extensions

Verify PHP version:
```bash
php -v
# Should show PHP 8.0 or higher
```

Check extensions:
```bash
php -m | grep -E '(PDO|pdo_mysql|mbstring)'
# All three should be listed
```

### 2. Syntax Validation

Test all PHP files for syntax errors:
```bash
find src public -name "*.php" -exec php -l {} \;
# All files should show "No syntax errors detected"
```

### 3. Database Schema

Test the SQL schema can be loaded:
```bash
mysql -u root -p -e "CREATE DATABASE test_chronos;"
mysql -u root -p test_chronos < database/schema.sql
mysql -u root -p test_chronos -e "SHOW TABLES;"
# Should show: players, weapons, servers, maps, rounds, events
mysql -u root -p -e "DROP DATABASE test_chronos;"
```

### 4. Sample Data

Load sample data for testing:
```bash
mysql -u root -p chronos_stats < database/sample_data.sql
```

### 5. Test Configuration

Create a test configuration:
```bash
cp config/config.sample.php config/config.php
# Edit with your database credentials
nano config/config.php
```

### 6. Web Interface Testing

Start a PHP development server:
```bash
cd public
php -S localhost:8000
```

Visit in your browser:
- http://localhost:8000/ - Player rankings
- http://localhost:8000/weapons.php - Weapon statistics
- http://localhost:8000/servers.php - Server list
- http://localhost:8000/search.php - Player search

### 7. API Testing

Test the tracker API endpoint:

```bash
curl -X POST http://localhost:8000/api/track.php \
  -H "Content-Type: application/json" \
  -d '{
    "server": {
      "name": "Test Server",
      "ip": "127.0.0.1",
      "port": 17300
    },
    "player": {
      "name": "TestPlayer",
      "hash": "test_hash_001",
      "kills": 10,
      "deaths": 5,
      "score": 1000
    },
    "weapons": [
      {
        "name": "M4A1",
        "kills": 10,
        "shots": 100,
        "hits": 50
      }
    ]
  }'
```

Expected response:
```json
{
  "success": true,
  "player_id": 1,
  "server_id": 1,
  "message": "Statistics updated successfully"
}
```

## PHP 8 Feature Verification

The codebase uses modern PHP 8 features:

### Type Declarations
```php
public function getTopPlayers(int $limit = 50, int $offset = 0): array
```

### Nullsafe Operators
```php
$this->config['charset'] ?? 'utf8mb4'
```

### Named Arguments (available)
```php
$db = Database::getInstance(config: $config);
```

### Constructor Property Promotion (could be used)
```php
class Player {
    public function __construct(private Database $db) {}
}
```

## MariaDB 11 Feature Verification

The database schema uses MariaDB 11 / MySQL 8 features:

### UTF-8 Support (utf8mb4)
```sql
DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
```

### Foreign Key Constraints
```sql
CONSTRAINT `weapons_player_fk` FOREIGN KEY (`player_id`) 
  REFERENCES `players` (`id`) ON DELETE CASCADE
```

### Views
```sql
CREATE OR REPLACE VIEW `player_rankings` AS ...
```

### Auto-increment with InnoDB
```sql
`id` int(11) NOT NULL AUTO_INCREMENT
```

## Performance Testing

### Database Query Performance
```bash
mysql -u chronos_user -p chronos_stats -e "
  EXPLAIN SELECT * FROM player_rankings LIMIT 50;
"
```

### Index Usage
```bash
mysql -u chronos_user -p chronos_stats -e "
  SHOW INDEX FROM players;
"
```

## Security Testing

### SQL Injection Prevention
All queries use PDO prepared statements:
```php
$stmt = $this->connection->prepare($sql);
$stmt->execute($params);
```

### XSS Prevention
All output is escaped:
```php
<?= html($player['player_name']) ?>
```

## Common Issues and Solutions

### Issue: "Database connection failed"
**Solution:** Check `config/config.php` credentials and ensure MariaDB/MySQL is running.

### Issue: "Class not found"
**Solution:** Ensure PSR-4 autoloading is working. Check namespace declarations.

### Issue: "Syntax error"
**Solution:** Verify PHP version is 8.0+. Check for PHP 8 compatibility.

### Issue: "Missing extension"
**Solution:** Install required PHP extensions via package manager.

## Continuous Integration

For automated testing in CI/CD pipelines:

```yaml
# Example GitHub Actions workflow
- name: Setup PHP
  uses: shivammathur/setup-php@v2
  with:
    php-version: '8.0'
    extensions: pdo, pdo_mysql, mbstring

- name: Validate Syntax
  run: find . -name "*.php" -exec php -l {} \;
```

## Test Coverage Checklist

- [x] PHP 8+ compatibility
- [x] Required extensions present
- [x] All PHP files syntax valid
- [x] Database schema loads successfully
- [x] Classes can be instantiated
- [x] Sample data imports correctly
- [x] Web interface accessible
- [x] API endpoint responds
- [x] PDO prepared statements used
- [x] HTML output is escaped
- [x] Foreign keys enforced
- [x] UTF-8 (utf8mb4) charset used

## Conclusion

Chronos V3 has been fully tested and validated for:
- ✅ PHP 8 compatibility
- ✅ MariaDB 11 / MySQL 8 compatibility
- ✅ Modern security practices
- ✅ Legacy BAB.Stats functionality
- ✅ Performance optimization

The system is production-ready for deployment on modern hosting environments.
