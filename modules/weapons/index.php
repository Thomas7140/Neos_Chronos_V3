<?php
/**
 * Chronos V3 - Weapons Statistics
 * 
 * Display weapon usage statistics.
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

// Get total weapon stats
$totalWeapons = $db->fetchOne(
    "SELECT COUNT(DISTINCT weapon_name) as count FROM " . TABLE_WEAPONS
);
$total = $totalWeapons['count'] ?? 0;

// Calculate pagination
$pagination = paginate($total, STATS_PER_PAGE, $page);

// Fetch weapon statistics
$weaponStats = $db->fetchAll(
    "SELECT 
        weapon_name,
        SUM(kills) as total_kills,
        SUM(deaths) as total_deaths,
        SUM(headshots) as total_headshots,
        SUM(shots_fired) as total_shots_fired,
        SUM(shots_hit) as total_shots_hit,
        CASE 
            WHEN SUM(shots_fired) > 0 
            THEN (SUM(shots_hit) * 100.0 / SUM(shots_fired))
            ELSE 0 
        END as accuracy,
        COUNT(DISTINCT player_id) as users
     FROM " . TABLE_WEAPONS . "
     GROUP BY weapon_name
     ORDER BY total_kills DESC
     LIMIT ? OFFSET ?",
    [STATS_PER_PAGE, $pagination['offset']]
);

$pageTitle = 'Weapon Statistics - ' . APP_NAME;
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
            <p class="subtitle">Weapon Statistics</p>
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
                <h2>Weapon Usage Statistics</h2>
                
                <table class="stats-table">
                    <thead>
                        <tr>
                            <th>Rank</th>
                            <th>Weapon</th>
                            <th>Total Kills</th>
                            <th>Total Deaths</th>
                            <th>Headshots</th>
                            <th>Accuracy</th>
                            <th>Users</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($weaponStats)): ?>
                            <tr>
                                <td colspan="7" class="no-data">No weapon data available</td>
                            </tr>
                        <?php else: ?>
                            <?php 
                            $rank = $pagination['offset'] + 1;
                            foreach ($weaponStats as $weapon): 
                            ?>
                                <tr>
                                    <td><?php echo $rank++; ?></td>
                                    <td class="player-name"><?php echo sanitize($weapon['weapon_name'], 'html'); ?></td>
                                    <td><?php echo formatNumber($weapon['total_kills']); ?></td>
                                    <td><?php echo formatNumber($weapon['total_deaths']); ?></td>
                                    <td><?php echo formatNumber($weapon['total_headshots']); ?></td>
                                    <td><?php echo formatNumber($weapon['accuracy'], 2); ?>%</td>
                                    <td><?php echo formatNumber($weapon['users']); ?></td>
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
