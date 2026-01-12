<?php
/**
 * Chronos V3 - Maps Statistics
 * 
 * Display map statistics.
 * 
 * @package ChronosV3
 * @version 3.0.0
 */

// Initialize application
define('CHRONOS_INIT', true);
require_once __DIR__ . '/../../config.php';
require_once INCLUDES_PATH . '/database.php';
require_once INCLUDES_PATH . '/functions.php';

// Start session
session_start();

// Get database instance
$db = Database::getInstance();

// Get current page for pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$page = max(1, $page);

// Get total maps
$totalMaps = $db->fetchOne(
    "SELECT COUNT(DISTINCT map_name) as count FROM " . TABLE_MAPS
);
$total = $totalMaps['count'] ?? 0;

// Calculate pagination
$pagination = paginate($total, STATS_PER_PAGE, $page);

// Fetch map statistics
$mapStats = $db->fetchAll(
    "SELECT 
        map_name,
        SUM(kills) as total_kills,
        SUM(deaths) as total_deaths,
        SUM(wins) as total_wins,
        SUM(losses) as total_losses,
        SUM(play_time) as total_play_time,
        COUNT(DISTINCT player_id) as unique_players,
        (SUM(wins) + SUM(losses)) as times_played
     FROM " . TABLE_MAPS . "
     GROUP BY map_name
     ORDER BY times_played DESC, total_kills DESC
     LIMIT ? OFFSET ?",
    [STATS_PER_PAGE, $pagination['offset']]
);

$pageTitle = 'Map Statistics - ' . APP_NAME;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo sanitize($pageTitle, 'html'); ?></title>
    <link rel="stylesheet" href="../../templates/style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1><?php echo sanitize(APP_NAME, 'html'); ?></h1>
            <p class="subtitle">Map Statistics</p>
        </header>

        <nav>
            <ul>
                <li><a href="../../index.php">Home</a></li>
                <li><a href="../../index.php">Players</a></li>
                <li><a href="../weapons/index.php">Weapons</a></li>
                <li><a href="../maps/index.php">Maps</a></li>
                <li><a href="../awards/index.php">Awards</a></li>
                <li><a href="../../admin/login.php">Admin</a></li>
            </ul>
        </nav>

        <main>
            <section class="leaderboard">
                <h2>Most Played Maps</h2>
                
                <table class="stats-table">
                    <thead>
                        <tr>
                            <th>Rank</th>
                            <th>Map Name</th>
                            <th>Times Played</th>
                            <th>Total Kills</th>
                            <th>Total Deaths</th>
                            <th>Wins</th>
                            <th>Losses</th>
                            <th>Players</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($mapStats)): ?>
                            <tr>
                                <td colspan="8" class="no-data">No map data available</td>
                            </tr>
                        <?php else: ?>
                            <?php 
                            $rank = $pagination['offset'] + 1;
                            foreach ($mapStats as $map): 
                            ?>
                                <tr>
                                    <td><?php echo $rank++; ?></td>
                                    <td class="player-name"><?php echo sanitize($map['map_name'], 'html'); ?></td>
                                    <td><?php echo formatNumber($map['times_played']); ?></td>
                                    <td><?php echo formatNumber($map['total_kills']); ?></td>
                                    <td><?php echo formatNumber($map['total_deaths']); ?></td>
                                    <td><?php echo formatNumber($map['total_wins']); ?></td>
                                    <td><?php echo formatNumber($map['total_losses']); ?></td>
                                    <td><?php echo formatNumber($map['unique_players']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>

                <?php if ($pagination['total_pages'] > 1): ?>
                    <div class="pagination">
                        <?php if ($pagination['has_prev']): ?>
                            <a href="?page=<?php echo $page - 1; ?>" class="page-link">« Previous</a>
                        <?php endif; ?>
                        
                        <span class="page-info">
                            Page <?php echo $pagination['current_page']; ?> of <?php echo $pagination['total_pages']; ?>
                        </span>
                        
                        <?php if ($pagination['has_next']): ?>
                            <a href="?page=<?php echo $page + 1; ?>" class="page-link">Next »</a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </section>
        </main>

        <footer>
            <p>&copy; <?php echo date('Y'); ?> <?php echo sanitize(APP_NAME, 'html'); ?> v<?php echo APP_VERSION; ?></p>
        </footer>
    </div>
</body>
</html>
