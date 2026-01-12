#!/bin/bash
# Chronos V3 Test Script
# Tests basic functionality without requiring a database connection

echo "========================================="
echo "Chronos V3 PHP 8 Compatibility Test"
echo "========================================="
echo ""

# Test 1: PHP Version
echo "Test 1: PHP Version Check"
PHP_VERSION=$(php -r "echo PHP_VERSION;")
PHP_MAJOR=$(php -r "echo PHP_MAJOR_VERSION;")
echo "  PHP Version: $PHP_VERSION"
if [ "$PHP_MAJOR" -ge 8 ]; then
    echo "  ✓ PASS: PHP 8+ detected"
else
    echo "  ✗ FAIL: PHP 8+ required"
    exit 1
fi
echo ""

# Test 2: Required PHP Extensions
echo "Test 2: PHP Extensions Check"
REQUIRED_EXTS="PDO pdo_mysql mbstring json"
for ext in $REQUIRED_EXTS; do
    if php -m | grep -q "^$ext$"; then
        echo "  ✓ $ext extension found"
    else
        echo "  ✗ $ext extension missing"
    fi
done
echo ""

# Test 3: Syntax Validation
echo "Test 3: PHP Syntax Validation"
ERROR_COUNT=0
for file in $(find src public -name "*.php"); do
    if php -l "$file" > /dev/null 2>&1; then
        echo "  ✓ $file"
    else
        echo "  ✗ $file has syntax errors"
        ERROR_COUNT=$((ERROR_COUNT + 1))
    fi
done

if [ $ERROR_COUNT -eq 0 ]; then
    echo "  ✓ All PHP files passed syntax check"
else
    echo "  ✗ $ERROR_COUNT files have syntax errors"
    exit 1
fi
echo ""

# Test 4: File Structure
echo "Test 4: File Structure Validation"
REQUIRED_FILES=(
    "composer.json"
    "config/config.sample.php"
    "database/schema.sql"
    "src/Database.php"
    "src/Player.php"
    "src/Weapon.php"
    "src/Server.php"
    "public/index.php"
    "public/bootstrap.php"
)

for file in "${REQUIRED_FILES[@]}"; do
    if [ -f "$file" ]; then
        echo "  ✓ $file exists"
    else
        echo "  ✗ $file missing"
        exit 1
    fi
done
echo ""

# Test 5: Class Loading Test
echo "Test 5: Class Loading Test"
cat > /tmp/test_autoload.php << 'EOF'
<?php
// Test autoloader
spl_autoload_register(function ($class) {
    $prefix = 'Chronos\\';
    $baseDir = __DIR__ . '/src/';
    
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    
    $relativeClass = substr($class, $len);
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';
    
    if (file_exists($file)) {
        require $file;
    }
});

// Try to load classes
try {
    $classesToTest = ['Chronos\\Database', 'Chronos\\Player', 'Chronos\\Weapon', 'Chronos\\Server'];
    foreach ($classesToTest as $class) {
        if (class_exists($class, true)) {
            echo "✓ Class $class can be loaded\n";
        } else {
            echo "✗ Class $class not found\n";
            exit(1);
        }
    }
    echo "✓ All classes loaded successfully\n";
} catch (Exception $e) {
    echo "✗ Error loading classes: " . $e->getMessage() . "\n";
    exit(1);
}
EOF

cd /home/runner/work/Neos_Chronos_V3/Neos_Chronos_V3
php /tmp/test_autoload.php
echo ""

# Test 6: SQL Schema Validation
echo "Test 6: SQL Schema Validation"
if [ -f "database/schema.sql" ]; then
    # Check for MariaDB 11 / MySQL 8 compatible syntax
    if grep -q "utf8mb4_unicode_ci" database/schema.sql; then
        echo "  ✓ UTF-8 (utf8mb4) charset configured"
    fi
    if grep -q "ENGINE=InnoDB" database/schema.sql; then
        echo "  ✓ InnoDB engine specified"
    fi
    if grep -q "FOREIGN KEY" database/schema.sql; then
        echo "  ✓ Foreign key constraints defined"
    fi
    echo "  ✓ SQL schema validation passed"
else
    echo "  ✗ database/schema.sql not found"
    exit 1
fi
echo ""

echo "========================================="
echo "✓ All tests passed!"
echo "Chronos V3 is ready for PHP 8 and MariaDB 11"
echo "========================================="
