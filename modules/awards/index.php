<?php
/**
 * Chronos V3 - Awards Display
 * 
 * Display available awards and top award earners.
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

// Fetch all available awards
$awards = $db->fetchAll(
    "SELECT * FROM " . TABLE_AWARDS . " 
     WHERE is_active = 1 
     ORDER BY award_type, award_name"
);

// Fetch top award earners
$topAwardEarners = $db->fetchAll(
    "SELECT 
        p.player_name,
        COUNT(DISTINCT pa.award_id) as awards_count,
        SUM(pa.times_earned) as total_awards
     FROM " . TABLE_PLAYERS . " p
     INNER JOIN chronos_player_awards pa ON p.id = pa.player_id
     GROUP BY p.id, p.player_name
     ORDER BY awards_count DESC, total_awards DESC
     LIMIT 20"
);

// Group awards by type
$awardsByType = [];
foreach ($awards as $award) {
    $type = $award['award_type'];
    if (!isset($awardsByType[$type])) {
        $awardsByType[$type] = [];
    }
    $awardsByType[$type][] = $award;
}

$pageTitle = 'Awards - ' . APP_NAME;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo sanitize($pageTitle, 'html'); ?></title>
    <link rel="stylesheet" href="../../templates/style.css">
    <style>
        .awards-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
            margin: 30px 0;
        }
        
        .award-card {
            background: white;
            border: 2px solid var(--border-color);
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            transition: all 0.3s;
        }
        
        .award-card:hover {
            border-color: var(--accent-color);
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        .award-icon {
            width: 64px;
            height: 64px;
            margin: 0 auto 15px;
            background: linear-gradient(135deg, var(--accent-color), #2980b9);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
        }
        
        .award-name {
            font-size: 1.2em;
            font-weight: bold;
            color: var(--primary-color);
            margin-bottom: 10px;
        }
        
        .award-description {
            color: var(--light-text);
            font-size: 0.9em;
            line-height: 1.4;
        }
        
        .award-type-section {
            margin: 40px 0;
        }
        
        .award-type-section h3 {
            color: var(--primary-color);
            border-bottom: 2px solid var(--accent-color);
            padding-bottom: 10px;
            margin-bottom: 20px;
            text-transform: capitalize;
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1><?php echo sanitize(APP_NAME, 'html'); ?></h1>
            <p class="subtitle">Player Awards & Achievements</p>
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
            <section>
                <h2>Available Awards</h2>
                
                <?php foreach ($awardsByType as $type => $typeAwards): ?>
                    <div class="award-type-section">
                        <h3><?php echo sanitize(ucfirst($type), 'html'); ?> Awards</h3>
                        <div class="awards-grid">
                            <?php foreach ($typeAwards as $award): ?>
                                <div class="award-card">
                                    <div class="award-icon">🏆</div>
                                    <div class="award-name"><?php echo sanitize($award['award_name'], 'html'); ?></div>
                                    <div class="award-description">
                                        <?php echo sanitize($award['award_description'], 'html'); ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
                
                <?php if (empty($awardsByType)): ?>
                    <p class="no-data" style="text-align: center; padding: 40px;">No awards configured yet.</p>
                <?php endif; ?>
            </section>

            <?php if (!empty($topAwardEarners)): ?>
                <section class="leaderboard">
                    <h2>Top Award Earners</h2>
                    
                    <table class="stats-table">
                        <thead>
                            <tr>
                                <th>Rank</th>
                                <th>Player Name</th>
                                <th>Unique Awards</th>
                                <th>Total Awards</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $rank = 1;
                            foreach ($topAwardEarners as $earner): 
                            ?>
                                <tr>
                                    <td><?php echo $rank++; ?></td>
                                    <td class="player-name"><?php echo sanitize($earner['player_name'], 'html'); ?></td>
                                    <td><?php echo formatNumber($earner['awards_count']); ?></td>
                                    <td><?php echo formatNumber($earner['total_awards']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </section>
            <?php endif; ?>
        </main>

        <footer>
            <p>&copy; <?php echo date('Y'); ?> <?php echo sanitize(APP_NAME, 'html'); ?> v<?php echo APP_VERSION; ?></p>
        </footer>
    </div>
</body>
</html>
