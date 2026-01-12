<?php
/**
 * Chronos V3 - Player Detail Page
 */

$config = require __DIR__ . '/bootstrap.php';

$playerId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($playerId <= 0) {
    header('Location: index.php');
    exit;
}

$playerClass = new \Chronos\Player($db);
$weaponClass = new \Chronos\Weapon($db);

$player = $playerClass->getPlayerById($playerId);

if (!$player) {
    header('Location: index.php');
    exit;
}

$weapons = $weaponClass->getPlayerWeaponStats($playerId);
$kdRatio = \Chronos\Player::calculateKDRatio($player['kills'], $player['deaths']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= html($config['site']['title']) ?> - <?= html($player['player_name']) ?></title>
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

        <h2>Player Profile: <?= html($player['player_name']) ?></h2>

        <div class="info-box">
            <table style="width: 100%; border: none;">
                <tr>
                    <td style="border: none; width: 50%;"><strong>First Seen:</strong> <?= html($player['first_seen']) ?></td>
                    <td style="border: none; width: 50%;"><strong>Last Seen:</strong> <?= html($player['last_seen']) ?></td>
                </tr>
            </table>
        </div>

        <h3>Overall Statistics</h3>
        <table>
            <tr>
                <th>Score</th>
                <th>Kills</th>
                <th>Deaths</th>
                <th>K/D Ratio</th>
                <th>Suicides</th>
                <th>Team Kills</th>
                <th>Headshots</th>
            </tr>
            <tr>
                <td class="stat-value stat-positive"><?= formatNumber($player['score']) ?></td>
                <td class="stat-value"><?= formatNumber($player['kills']) ?></td>
                <td class="stat-value"><?= formatNumber($player['deaths']) ?></td>
                <td class="stat-value"><?= number_format($kdRatio, 2) ?></td>
                <td class="stat-value stat-negative"><?= formatNumber($player['suicides']) ?></td>
                <td class="stat-value stat-negative"><?= formatNumber($player['teamkills']) ?></td>
                <td class="stat-value"><?= formatNumber($player['headshots']) ?></td>
            </tr>
        </table>

        <h3>Game Statistics</h3>
        <table>
            <tr>
                <th>Rounds Played</th>
                <th>Wins</th>
                <th>Losses</th>
                <th>Win Rate</th>
                <th>Playtime</th>
                <th>Avg Score/Round</th>
            </tr>
            <tr>
                <td class="stat-value"><?= formatNumber($player['rounds_played']) ?></td>
                <td class="stat-value stat-positive"><?= formatNumber($player['wins']) ?></td>
                <td class="stat-value stat-negative"><?= formatNumber($player['losses']) ?></td>
                <td class="stat-value">
                    <?php 
                    $totalGames = $player['wins'] + $player['losses'];
                    $winRate = $totalGames > 0 ? ($player['wins'] / $totalGames) * 100 : 0;
                    echo number_format($winRate, 1) . '%';
                    ?>
                </td>
                <td class="stat-value"><?= formatTime($player['playtime']) ?></td>
                <td class="stat-value">
                    <?= $player['rounds_played'] > 0 
                        ? number_format($player['score'] / $player['rounds_played'], 1) 
                        : '0' ?>
                </td>
            </tr>
        </table>

        <?php if (!empty($weapons)): ?>
            <h3>Weapon Statistics</h3>
            <table>
                <thead>
                    <tr>
                        <th>Weapon</th>
                        <th style="text-align: right;">Kills</th>
                        <th style="text-align: right;">Deaths</th>
                        <th style="text-align: right;">Headshots</th>
                        <th style="text-align: right;">Shots</th>
                        <th style="text-align: right;">Hits</th>
                        <th style="text-align: right;">Accuracy</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($weapons as $index => $weapon): ?>
                        <tr class="<?= $index % 2 === 0 ? 'even' : 'odd' ?>">
                            <td><?= html($weapon['weapon_name']) ?></td>
                            <td class="stat-value stat-positive"><?= formatNumber($weapon['kills']) ?></td>
                            <td class="stat-value stat-negative"><?= formatNumber($weapon['deaths']) ?></td>
                            <td class="stat-value"><?= formatNumber($weapon['headshots']) ?></td>
                            <td class="stat-value"><?= formatNumber($weapon['shots']) ?></td>
                            <td class="stat-value"><?= formatNumber($weapon['hits']) ?></td>
                            <td class="stat-value"><?= number_format($weapon['accuracy'], 2) ?>%</td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <div class="footer">
            <a href="index.php">« Back to Rankings</a> | 
            BAB.Stats Neos Chronos V3 &copy; <?= date('Y') ?>
        </div>
    </div>
</body>
</html>
