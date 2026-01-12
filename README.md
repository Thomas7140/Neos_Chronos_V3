# BAB.Stats Neos Chronos V3

[![PHP](https://img.shields.io/badge/PHP-8.0%2B-blue.svg)](https://www.php.net/)
[![MariaDB](https://img.shields.io/badge/MariaDB-11-green.svg)](https://mariadb.org/)
[![License](https://img.shields.io/badge/License-MIT-yellow.svg)](LICENSE)

**Chronos V3** is the modern continuation of the original **Neos Chronos V2** statistics system for **Black Hawk Down (BHD)** and **BHD Team Sabre (BHDTS)**.

Originally built for PHP 4 and MySQL, Chronos has now been **fully rewritten and modernized** to run on **PHP 8** and **MariaDB 11**, ensuring long-term compatibility, improved performance, and better security.

This release brings the classic **BAB.Stats Neos Chronos** experience into today's hosting environments while preserving full compatibility with existing stat formats and trackers.

## ✨ What's New in Chronos V3

- ✅ **Fully upgraded from PHP 4 → PHP 8**
  - Modern object-oriented architecture
  - Type safety and improved error handling
  - Namespace support for better code organization
  - PDO for secure database operations
  
- ✅ **Database layer modernized for MariaDB 11**
  - UTF-8 (utf8mb4) character set support
  - Prepared statements for SQL injection protection
  - Optimized queries and indexes
  - Foreign key constraints for data integrity
  
- ✅ **Improved stability, speed, and security**
  - Modern PHP 8 features and best practices
  - Secure password handling
  - XSS protection with HTML escaping
  - Session management
  
- ✅ **Compatible with modern hosting**
  - Works on shared hosting, VPS, and dedicated servers
  - Minimal resource requirements
  - Easy installation and configuration
  
- ✅ **Legacy BAB.Stats look and functionality preserved**
  - Classic dark theme design
  - Familiar navigation and layout
  - Full backward compatibility with tracker formats

## 📋 Features

- **Player Statistics**
  - Individual player profiles
  - Kills, deaths, K/D ratios
  - Score tracking and rankings
  - Playtime and rounds played
  - Weapon preferences and accuracy

- **Weapon Statistics**
  - Overall weapon usage stats
  - Accuracy tracking
  - Headshot statistics
  - Kill/death ratios per weapon

- **Server Tracking**
  - Multiple server support
  - Map popularity statistics
  - Server activity monitoring
  - Round history

- **Search Functionality**
  - Quick player search
  - Name-based lookups
  - Comprehensive result display

## 🚀 Requirements

- **PHP 8.0 or higher**
  - Required extensions: PDO, pdo_mysql, mbstring
- **MariaDB 11** or **MySQL 8+**
- **Web server** (Apache, Nginx, or similar)
- Basic command-line access (for initial setup)

## 📦 Installation

See [INSTALL.md](INSTALL.md) for detailed installation instructions.

### Quick Start

1. **Clone the repository**
   ```bash
   git clone https://github.com/Thomas7140/Neos_Chronos_V3.git
   cd Neos_Chronos_V3
   ```

2. **Configure the database**
   ```bash
   cp config/config.sample.php config/config.php
   # Edit config/config.php with your database credentials
   ```

3. **Import the database schema**
   ```bash
   mysql -u username -p chronos_stats < database/schema.sql
   ```

4. **Configure your web server**
   - Point document root to the `public/` directory
   - Enable mod_rewrite (for Apache)

5. **Access the application**
   - Navigate to your configured URL
   - Start tracking statistics!

## 🔧 Configuration

Edit `config/config.php` to customize:

- Database connection settings
- Site title and URL
- Timezone settings
- Statistics display options
- Security settings

See `config/config.sample.php` for all available options.

## 📁 Project Structure

```
Neos_Chronos_V3/
├── config/              # Configuration files
│   └── config.sample.php
├── database/            # Database schema and migrations
│   └── schema.sql
├── public/              # Web-accessible files
│   ├── css/
│   │   └── style.css
│   ├── bootstrap.php    # Application initialization
│   ├── index.php        # Player rankings
│   ├── player.php       # Player detail page
│   ├── weapons.php      # Weapon statistics
│   ├── servers.php      # Server listing
│   └── search.php       # Player search
├── src/                 # PHP classes (PSR-4 autoloaded)
│   ├── Database.php     # Database connection class
│   ├── Player.php       # Player statistics class
│   ├── Weapon.php       # Weapon statistics class
│   └── Server.php       # Server management class
└── composer.json        # Dependency management
```

## 🛠️ Development

Chronos V3 follows modern PHP development practices:

- **PSR-4 autoloading** for classes
- **Namespaced code** (`Chronos\*`)
- **Type declarations** throughout
- **PDO prepared statements** for all database queries
- **HTML escaping** for XSS protection

## 🔐 Security

Chronos V3 implements several security measures:

- Prepared statements prevent SQL injection
- HTML entity encoding prevents XSS
- Secure session handling
- Input validation and sanitization
- Modern PHP 8 security features

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 🤝 Contributing

Contributions are welcome! Please feel free to submit issues or pull requests.

## 📞 Support

For support, please open an issue on GitHub or contact the maintainers.

## 🙏 Acknowledgments

- Original Neos Chronos V2 development team
- BAB.Stats community
- Black Hawk Down gaming community

## 📜 Changelog

### Version 3.0.0 (2026)
- Complete rewrite for PHP 8 and MariaDB 11
- Modern object-oriented architecture
- Improved security and performance
- Enhanced database schema
- Preserved classic BAB.Stats interface

---

**BAB.Stats Neos Chronos V3** - Bringing classic game statistics into the modern era 🎮