<?php
/**
 * Chronos V3 - Main Entry Point
 * 
 * Displays player statistics, leaderboards, and rankings.
 * 
 * @package ChronosV3
 * @version 3.0.0
 */

// Initialize application
define('CHRONOS_INIT', true);
require_once __DIR__ . '/config.php';
require_once INCLUDES_PATH . '/database.php';
require_once INCLUDES_PATH . '/functions.php';

// Start session
session_start();

// Get database instance
$db = Database::getInstance();

// Get current page for pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$page = max(1, $page);

// Get sort parameters
$sortBy = isset($_GET['sort']) ? sanitize($_GET['sort']) : 'rating';
$sortOrder = isset($_GET['order']) && $_GET['order'] === 'asc' ? 'ASC' : 'DESC';

// Validate sort column
$validSortColumns = ['rating', 'kills', 'deaths', 'kd_ratio', 'headshots', 'play_time', 'last_seen'];
if (!in_array($sortBy, $validSortColumns)) {
    $sortBy = 'rating';
}

// Get total players count
$totalPlayers = $db->fetchOne("SELECT COUNT(*) as count FROM " . TABLE_PLAYERS);
$total = $totalPlayers['count'] ?? 0;

// Calculate pagination
$pagination = paginate($total, STATS_PER_PAGE, $page);

// Fetch top players
$players = $db->fetchAll(
    "SELECT 
        p.*,
        CASE WHEN p.deaths = 0 THEN p.kills ELSE p.kills / p.deaths END as kd_ratio
     FROM " . TABLE_PLAYERS . " p
     ORDER BY {$sortBy} {$sortOrder}
     LIMIT ? OFFSET ?",
    [STATS_PER_PAGE, $pagination['offset']]
);

// Get server stats
$serverStats = $db->fetchOne(
    "SELECT 
        COUNT(*) as total_players,
        SUM(kills) as total_kills,
        SUM(deaths) as total_deaths,
        SUM(headshots) as total_headshots,
        SUM(play_time) as total_play_time
     FROM " . TABLE_PLAYERS
);

// Get top weapons
$topWeapons = $db->fetchAll(
    "SELECT weapon_name, SUM(kills) as total_kills
     FROM " . TABLE_WEAPONS . "
     GROUP BY weapon_name
     ORDER BY total_kills DESC
     LIMIT 5"
);

// Get top maps
$topMaps = $db->fetchAll(
    "SELECT map_name, COUNT(*) as times_played
     FROM " . TABLE_MAPS . "
     GROUP BY map_name
     ORDER BY times_played DESC
     LIMIT 5"
);

// Page title
$pageTitle = APP_NAME . ' - Player Statistics';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo sanitize($pageTitle, 'html'); ?></title>
    <link rel="stylesheet" href="templates/style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1><?php echo sanitize(APP_NAME, 'html'); ?></h1>
            <p class="subtitle">Statistics for <?php echo sanitize(SERVER_NAME, 'html'); ?></p>
        </header>

        <nav>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="modules/weapons/index.php">Weapons</a></li>
                <li><a href="modules/maps/index.php">Maps</a></li>
                <li><a href="modules/awards/index.php">Awards</a></li>
                <li><a href="admin/login.php">Admin</a></li>
            </ul>
        </nav>

        <main>
            <section class="stats-overview">
                <h2>Server Overview</h2>
                <div class="stats-grid">
                    <div class="stat-card">
                        <h3>Total Players</h3>
                        <p class="stat-value"><?php echo formatNumber($serverStats['total_players'] ?? 0); ?></p>
                    </div>
                    <div class="stat-card">
                        <h3>Total Kills</h3>
                        <p class="stat-value"><?php echo formatNumber($serverStats['total_kills'] ?? 0); ?></p>
                    </div>
                    <div class="stat-card">
                        <h3>Total Deaths</h3>
                        <p class="stat-value"><?php echo formatNumber($serverStats['total_deaths'] ?? 0); ?></p>
                    </div>
                    <div class="stat-card">
                        <h3>Total Headshots</h3>
                        <p class="stat-value"><?php echo formatNumber($serverStats['total_headshots'] ?? 0); ?></p>
                    </div>
                </div>
            </section>

            <section class="leaderboard">
                <h2>Top Players</h2>
                <div class="table-controls">
                    <form method="get" class="sort-form">
                        <label>Sort by:</label>
                        <select name="sort" onchange="this.form.submit()">
                            <option value="rating" <?php echo $sortBy === 'rating' ? 'selected' : ''; ?>>Rating</option>
                            <option value="kills" <?php echo $sortBy === 'kills' ? 'selected' : ''; ?>>Kills</option>
                            <option value="deaths" <?php echo $sortBy === 'deaths' ? 'selected' : ''; ?>>Deaths</option>
                            <option value="kd_ratio" <?php echo $sortBy === 'kd_ratio' ? 'selected' : ''; ?>>K/D Ratio</option>
                            <option value="headshots" <?php echo $sortBy === 'headshots' ? 'selected' : ''; ?>>Headshots</option>
                        </select>
                        <select name="order" onchange="this.form.submit()">
                            <option value="desc" <?php echo $sortOrder === 'DESC' ? 'selected' : ''; ?>>Descending</option>
                            <option value="asc" <?php echo $sortOrder === 'ASC' ? 'selected' : ''; ?>>Ascending</option>
                        </select>
                    </form>
                </div>

                <table class="stats-table">
                    <thead>
                        <tr>
                            <th>Rank</th>
                            <th>Player Name</th>
                            <th>Rating</th>
                            <th>Kills</th>
                            <th>Deaths</th>
                            <th>K/D Ratio</th>
                            <th>Headshots</th>
                            <th>Last Seen</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($players)): ?>
                            <tr>
                                <td colspan="8" class="no-data">No player data available</td>
                            </tr>
                        <?php else: ?>
                            <?php 
                            $rank = $pagination['offset'] + 1;
                            foreach ($players as $player): 
                            ?>
                                <tr>
                                    <td><?php echo $rank++; ?></td>
                                    <td class="player-name"><?php echo sanitize($player['player_name'], 'html'); ?></td>
                                    <td><?php echo formatNumber($player['rating']); ?></td>
                                    <td><?php echo formatNumber($player['kills']); ?></td>
                                    <td><?php echo formatNumber($player['deaths']); ?></td>
                                    <td><?php echo formatKD($player['kills'], $player['deaths']); ?></td>
                                    <td><?php echo formatNumber($player['headshots']); ?></td>
                                    <td><?php echo formatDateTime($player['last_seen'], 'Y-m-d'); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>

                <?php if ($pagination['total_pages'] > 1): ?>
                    <div class="pagination">
                        <?php if ($pagination['has_prev']): ?>
                            <a href="?page=<?php echo $page - 1; ?>&sort=<?php echo $sortBy; ?>&order=<?php echo strtolower($sortOrder); ?>" class="page-link">« Previous</a>
                        <?php endif; ?>
                        
                        <span class="page-info">
                            Page <?php echo $pagination['current_page']; ?> of <?php echo $pagination['total_pages']; ?>
                        </span>
                        
                        <?php if ($pagination['has_next']): ?>
                            <a href="?page=<?php echo $page + 1; ?>&sort=<?php echo $sortBy; ?>&order=<?php echo strtolower($sortOrder); ?>" class="page-link">Next »</a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </section>
        </main>

        <footer>
            <p>&copy; <?php echo date('Y'); ?> <?php echo sanitize(APP_NAME, 'html'); ?> v<?php echo APP_VERSION; ?> - 
            <a href="https://github.com/Thomas7140/Neos_Chronos_V3">GitHub</a></p>
        </footer>
    </div>
</body>
</html>
