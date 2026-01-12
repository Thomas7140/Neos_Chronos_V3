<?php
/**
 * Chronos V3 - Weapon Statistics Page
 */

$config = require __DIR__ . '/bootstrap.php';

$weaponClass = new \Chronos\Weapon($db);

// Get weapon statistics
$weapons = $weaponClass->getWeaponStats(100);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= html($config['site']['title']) ?> - Weapon Statistics</title>
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

        <h2>Weapon Statistics</h2>
        
        <?php if (empty($weapons)): ?>
            <div class="no-data">No weapon statistics available yet.</div>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Weapon</th>
                        <th style="text-align: right;">Kills</th>
                        <th style="text-align: right;">Deaths</th>
                        <th style="text-align: right;">K/D</th>
                        <th style="text-align: right;">Headshots</th>
                        <th style="text-align: right;">Shots</th>
                        <th style="text-align: right;">Hits</th>
                        <th style="text-align: right;">Accuracy</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($weapons as $index => $weapon): ?>
                        <tr class="<?= $index % 2 === 0 ? 'even' : 'odd' ?>">
                            <td class="player-name"><?= html($weapon['weapon_name']) ?></td>
                            <td class="stat-value stat-positive"><?= formatNumber($weapon['total_kills']) ?></td>
                            <td class="stat-value stat-negative"><?= formatNumber($weapon['total_deaths']) ?></td>
                            <td class="stat-value">
                                <?= number_format(
                                    $weapon['total_deaths'] > 0 
                                        ? $weapon['total_kills'] / $weapon['total_deaths'] 
                                        : $weapon['total_kills'], 
                                    2
                                ) ?>
                            </td>
                            <td class="stat-value"><?= formatNumber($weapon['total_headshots']) ?></td>
                            <td class="stat-value"><?= formatNumber($weapon['total_shots']) ?></td>
                            <td class="stat-value"><?= formatNumber($weapon['total_hits']) ?></td>
                            <td class="stat-value"><?= number_format($weapon['accuracy'], 2) ?>%</td>
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
