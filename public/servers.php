<?php
/**
 * Chronos V3 - Server List Page
 */

$config = require __DIR__ . '/bootstrap.php';

$serverClass = new \Chronos\Server($db);

$servers = $serverClass->getAllServers();
$maps = $serverClass->getPopularMaps(20);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= html($config['site']['title']) ?> - Servers</title>
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

        <h2>Tracked Servers</h2>
        
        <?php if (empty($servers)): ?>
            <div class="no-data">No servers tracked yet.</div>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Server Name</th>
                        <th>Address</th>
                        <th>Current Map</th>
                        <th>Game Type</th>
                        <th style="text-align: right;">Players</th>
                        <th style="text-align: right;">Rounds</th>
                        <th>Last Seen</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($servers as $index => $server): ?>
                        <tr class="<?= $index % 2 === 0 ? 'even' : 'odd' ?>">
                            <td class="player-name"><?= html($server['server_name']) ?></td>
                            <td><?= html($server['server_ip']) ?>:<?= html($server['server_port']) ?></td>
                            <td><?= html($server['map_name'] ?? 'Unknown') ?></td>
                            <td><?= html($server['game_type'] ?? 'N/A') ?></td>
                            <td class="stat-value">
                                <?= html($server['current_players']) ?>/<?= html($server['max_players']) ?>
                            </td>
                            <td class="stat-value"><?= formatNumber($server['rounds_played']) ?></td>
                            <td><?= html($server['last_seen']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <?php if (!empty($maps)): ?>
            <h2>Popular Maps</h2>
            <table>
                <thead>
                    <tr>
                        <th>Map Name</th>
                        <th style="text-align: right;">Times Played</th>
                        <th style="text-align: right;">Total Playtime</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($maps as $index => $map): ?>
                        <tr class="<?= $index % 2 === 0 ? 'even' : 'odd' ?>">
                            <td class="player-name"><?= html($map['map_name']) ?></td>
                            <td class="stat-value"><?= formatNumber($map['times_played']) ?></td>
                            <td class="stat-value"><?= formatTime($map['total_playtime']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <div class="footer">
            BAB.Stats Neos Chronos V3 &copy; <?= date('Y') ?> | 
            Modernized for PHP 8 and MariaDB 11 | 
            Compatible with BHD and BHDTS
        </div>
    </div>
</body>
</html>
