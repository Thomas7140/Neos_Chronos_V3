<?php
/**
 * Chronos V3 - Main Index Page
 * Displays top players and overall statistics
 */

$config = require __DIR__ . '/bootstrap.php';

$playerClass = new \Chronos\Player($db);
$weaponClass = new \Chronos\Weapon($db);
$serverClass = new \Chronos\Server($db);

// Pagination
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$perPage = $config['site']['items_per_page'] ?? 50;
$offset = ($page - 1) * $perPage;

// Get statistics
$topPlayers = $playerClass->getTopPlayers($perPage, $offset);
$totalPlayers = $playerClass->getTotalPlayers();
$totalPages = ceil($totalPlayers / $perPage);

// Get top weapons
$topWeapons = $weaponClass->getWeaponStats(10);

// Get servers
$servers = $serverClass->getAllServers();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= html($config['site']['title']) ?> - Player Rankings</title>
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

        <div class="info-box">
            <strong>Total Players:</strong> <?= formatNumber($totalPlayers) ?> |
            <strong>Active Servers:</strong> <?= count($servers) ?> |
            <strong>Most Popular Weapon:</strong> <?= !empty($topWeapons) ? html($topWeapons[0]['weapon_name']) : 'N/A' ?>
        </div>

        <h2>Top Players</h2>
        
        <?php if (empty($topPlayers)): ?>
            <div class="no-data">No player statistics available yet.</div>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th class="rank">Rank</th>
                        <th>Player Name</th>
                        <th style="text-align: right;">Score</th>
                        <th style="text-align: right;">Kills</th>
                        <th style="text-align: right;">Deaths</th>
                        <th style="text-align: right;">K/D</th>
                        <th style="text-align: right;">Playtime</th>
                        <th style="text-align: right;">Rounds</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($topPlayers as $index => $player): ?>
                        <tr class="<?= $index % 2 === 0 ? 'even' : 'odd' ?>">
                            <td class="rank"><?= $offset + $index + 1 ?></td>
                            <td class="player-name">
                                <a href="player.php?id=<?= $player['id'] ?>">
                                    <?= html($player['player_name']) ?>
                                </a>
                            </td>
                            <td class="stat-value stat-positive"><?= formatNumber($player['score']) ?></td>
                            <td class="stat-value"><?= formatNumber($player['kills']) ?></td>
                            <td class="stat-value"><?= formatNumber($player['deaths']) ?></td>
                            <td class="stat-value"><?= number_format($player['kd_ratio'], 2) ?></td>
                            <td class="stat-value"><?= formatTime($player['playtime']) ?></td>
                            <td class="stat-value"><?= formatNumber($player['rounds_played']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <?php if ($totalPages > 1): ?>
                <div class="pagination">
                    <?php if ($page > 1): ?>
                        <a href="?page=<?= $page - 1 ?>">&laquo; Previous</a>
                    <?php endif; ?>
                    
                    <?php for ($i = max(1, $page - 5); $i <= min($totalPages, $page + 5); $i++): ?>
                        <?php if ($i === $page): ?>
                            <span class="current"><?= $i ?></span>
                        <?php else: ?>
                            <a href="?page=<?= $i ?>"><?= $i ?></a>
                        <?php endif; ?>
                    <?php endfor; ?>
                    
                    <?php if ($page < $totalPages): ?>
                        <a href="?page=<?= $page + 1 ?>">Next &raquo;</a>
                    <?php endif; ?>
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
