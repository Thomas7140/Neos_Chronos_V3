<?php
/**
 * Chronos V3 - Admin Panel
 * 
 * Main admin dashboard.
 * 
 * @package ChronosV3
 * @version 3.0.0
 */

// Initialize application
define('CHRONOS_INIT', true);
require_once __DIR__ . '/../config.php';
require_once INCLUDES_PATH . '/database.php';
require_once INCLUDES_PATH . '/functions.php';

// Start session
session_start();

// Require authentication
requireLogin();

// Get database instance
$db = Database::getInstance();

// Get statistics
$totalPlayers = $db->fetchOne("SELECT COUNT(*) as count FROM " . TABLE_PLAYERS)['count'] ?? 0;
$totalKills = $db->fetchOne("SELECT SUM(kills) as total FROM " . TABLE_PLAYERS)['total'] ?? 0;
$totalDeaths = $db->fetchOne("SELECT SUM(deaths) as total FROM " . TABLE_PLAYERS)['total'] ?? 0;
$totalGames = $db->fetchOne("SELECT SUM(games_played) as total FROM " . TABLE_PLAYERS)['total'] ?? 0;

// Get recent players
$recentPlayers = $db->fetchAll(
    "SELECT player_name, rating, kills, deaths, last_seen 
     FROM " . TABLE_PLAYERS . " 
     ORDER BY last_seen DESC 
     LIMIT 10"
);

$pageTitle = 'Admin Dashboard - ' . APP_NAME;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo sanitize($pageTitle, 'html'); ?></title>
    <link rel="stylesheet" href="../templates/style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Admin Dashboard</h1>
            <p class="subtitle">Welcome, <?php echo sanitize($_SESSION['admin_username'], 'html'); ?></p>
        </header>

        <nav>
            <ul>
                <li><a href="index.php">Dashboard</a></li>
                <li><a href="players.php">Manage Players</a></li>
                <li><a href="servers.php">Manage Servers</a></li>
                <li><a href="awards.php">Manage Awards</a></li>
                <li><a href="settings.php">Settings</a></li>
                <li><a href="../index.php">View Stats</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>

        <main>
            <section class="stats-overview">
                <h2>System Overview</h2>
                <div class="stats-grid">
                    <div class="stat-card">
                        <h3>Total Players</h3>
                        <p class="stat-value"><?php echo formatNumber($totalPlayers); ?></p>
                    </div>
                    <div class="stat-card">
                        <h3>Total Kills</h3>
                        <p class="stat-value"><?php echo formatNumber($totalKills); ?></p>
                    </div>
                    <div class="stat-card">
                        <h3>Total Deaths</h3>
                        <p class="stat-value"><?php echo formatNumber($totalDeaths); ?></p>
                    </div>
                    <div class="stat-card">
                        <h3>Total Games</h3>
                        <p class="stat-value"><?php echo formatNumber($totalGames); ?></p>
                    </div>
                </div>
            </section>

            <section class="leaderboard">
                <h2>Recent Players</h2>
                <table class="stats-table">
                    <thead>
                        <tr>
                            <th>Player Name</th>
                            <th>Rating</th>
                            <th>Kills</th>
                            <th>Deaths</th>
                            <th>Last Seen</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($recentPlayers)): ?>
                            <tr>
                                <td colspan="5" class="no-data">No player data available</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($recentPlayers as $player): ?>
                                <tr>
                                    <td class="player-name"><?php echo sanitize($player['player_name'], 'html'); ?></td>
                                    <td><?php echo formatNumber($player['rating']); ?></td>
                                    <td><?php echo formatNumber($player['kills']); ?></td>
                                    <td><?php echo formatNumber($player['deaths']); ?></td>
                                    <td><?php echo formatDateTime($player['last_seen']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </section>
        </main>

        <footer>
            <p>&copy; <?php echo date('Y'); ?> <?php echo sanitize(APP_NAME, 'html'); ?> v<?php echo APP_VERSION; ?></p>
        </footer>
    </div>
</body>
</html>
