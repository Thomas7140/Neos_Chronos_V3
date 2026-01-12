<?php
/**
 * Chronos V3 - Player Search Page
 */

$config = require __DIR__ . '/bootstrap.php';

$playerClass = new \Chronos\Player($db);

$searchQuery = isset($_GET['q']) ? trim($_GET['q']) : '';
$results = [];

if (!empty($searchQuery)) {
    $results = $playerClass->searchPlayers($searchQuery, 100);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= html($config['site']['title']) ?> - Player Search</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><?= html($config['site']['title']) ?></h1>
            <div class="subtitle">Black Hawk Down Statistics System - Version 3</div>
        </div>

        <div class="nav">
            <a href="index.php">Player Rankings</a>
            <a href="weapons.php">Weapon Stats</a>
            <a href="servers.php">Servers</a>
            <a href="search.php">Search</a>
        </div>

        <h2>Search Players</h2>

        <div class="search-box">
            <form method="get" action="search.php">
                <input type="text" name="q" value="<?= html($searchQuery) ?>" placeholder="Enter player name..." autofocus>
                <input type="submit" value="Search">
            </form>
        </div>

        <?php if (!empty($searchQuery)): ?>
            <h3>Search Results for "<?= html($searchQuery) ?>"</h3>
            
            <?php if (empty($results)): ?>
                <div class="no-data">No players found matching your search.</div>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Player Name</th>
                            <th style="text-align: right;">Score</th>
                            <th style="text-align: right;">Kills</th>
                            <th style="text-align: right;">Deaths</th>
                            <th style="text-align: right;">K/D</th>
                            <th style="text-align: right;">Playtime</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($results as $index => $player): ?>
                            <?php $kdRatio = \Chronos\Player::calculateKDRatio($player['kills'], $player['deaths']); ?>
                            <tr class="<?= $index % 2 === 0 ? 'even' : 'odd' ?>">
                                <td class="player-name">
                                    <a href="player.php?id=<?= $player['id'] ?>">
                                        <?= html($player['player_name']) ?>
                                    </a>
                                </td>
                                <td class="stat-value stat-positive"><?= formatNumber($player['score']) ?></td>
                                <td class="stat-value"><?= formatNumber($player['kills']) ?></td>
                                <td class="stat-value"><?= formatNumber($player['deaths']) ?></td>
                                <td class="stat-value"><?= number_format($kdRatio, 2) ?></td>
                                <td class="stat-value"><?= formatTime($player['playtime']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <div class="info-box">
                    Found <?= count($results) ?> player<?= count($results) !== 1 ? 's' : '' ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <div class="footer">
            BAB.Stats Neos Chronos V3 &copy; <?= date('Y') ?> | 
            Modernized for PHP 8 and MariaDB 11 | 
            Compatible with BHD and BHDTS
        </div>
    </div>
</body>
</html>
