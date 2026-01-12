# BAB.Stats Chronos – Version 3

Chronos V3 is the modern continuation of the original Neos Chronos V2 statistics system for Delta Force: Black Hawk Down (BHD) and Team Sabre (BHDTS).

## Features

- **Player Statistics**: Track kills, deaths, K/D ratio, headshots, points, and more
- **Weapon Statistics**: Detailed breakdown of weapon usage and performance
- **Map Statistics**: Analyze performance across different maps
- **Awards System**: Automatic achievement awards based on gameplay
- **Ranking System**: Competitive ranking and leaderboards
- **Squad/Clan Management**: Track team statistics and performance
- **Monthly Statistics**: Separate tracking for monthly performance
- **Hall of Fame**: Historical records of top players
- **Multi-Server Support**: Consolidate statistics from multiple game servers
- **Admin Panel**: Web-based administration interface
- **PHP 8+ Compatible**: Modern PHP with improved security
- **MySQL/MariaDB**: Robust database support

## Requirements

- PHP 8.0 or higher
- MySQL 5.7+ or MariaDB 10.3+
- Web server (Apache/Nginx)
- Delta Force: Black Hawk Down or Team Sabre game server

## Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/Thomas7140/Neos_Chronos_V3.git
   cd Neos_Chronos_V3
   ```

2. **Configure environment**
   ```bash
   cp .env.example .env
   ```
   Edit `.env` and set your database credentials and configuration.

3. **Set up the database**
   - Create a new MySQL/MariaDB database
   - Import the database schema:
     ```bash
     mysql -u username -p database_name < database/schema.sql
     ```

4. **Configure web server**
   - Point your web server document root to the installation directory
   - Ensure PHP has appropriate permissions

5. **Run the installer**
   - Navigate to `http://yourserver.com/install.php`
   - Follow the installation wizard

6. **Set up the stats uploader**
   - Configure the uploader tool on your game server
   - Point it to your Chronos V3 installation

## Configuration

Edit `config.php` to customize:
- Database connection settings
- Display preferences
- Rating calculation formulas
- Admin credentials
- Server settings

## Security

- Always change default admin credentials
- Keep PHP and database software up to date
- Use HTTPS for production deployments
- Regularly backup your database
- Review security settings in `config.php`

## Documentation

- [Installation Guide](docs/INSTALLATION.md)
- [Configuration Guide](docs/CONFIGURATION.md)
- [API Documentation](docs/API.md)
- [Troubleshooting](docs/TROUBLESHOOTING.md)

## Upgrading from V2

See the [Migration Guide](docs/MIGRATION.md) for instructions on upgrading from Neos Chronos V2.

## Contributing

Contributions are welcome! Please read our [Contributing Guidelines](CONTRIBUTING.md) before submitting pull requests.

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## Credits

Based on the original BAB.stats Chronos system, with contributions from the Black Hawk Down community.

## Support

- GitHub Issues: [Report bugs or request features](https://github.com/Thomas7140/Neos_Chronos_V3/issues)
- Community: Join the Black Hawk Down community forums

## Changelog

See [CHANGELOG.md](CHANGELOG.md) for version history and updates.