<?php
/**
 * Chronos V3 - API Tracker Endpoint
 * Receives game statistics from external trackers
 * 
 * Expected POST format (JSON):
 * {
 *   "server": {
 *     "name": "Server Name",
 *     "ip": "192.168.1.1",
 *     "port": 17300
 *   },
 *   "player": {
 *     "name": "PlayerName",
 *     "hash": "unique_player_hash",
 *     "kills": 10,
 *     "deaths": 5,
 *     "score": 1000
 *   },
 *   "weapons": [
 *     {"name": "M4A1", "kills": 5, "shots": 100, "hits": 40}
 *   ]
 * }
 */

header('Content-Type: application/json');

// Load configuration
$config = require __DIR__ . '/../bootstrap.php';

// Check if request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

// Get POST data
$rawData = file_get_contents('php://input');
$data = json_decode($rawData, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid JSON']);
    exit;
}

// Validate required fields
if (empty($data['player']) || empty($data['player']['name']) || empty($data['player']['hash'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing required player data']);
    exit;
}

try {
    $playerClass = new \Chronos\Player($db);
    $weaponClass = new \Chronos\Weapon($db);
    $serverClass = new \Chronos\Server($db);
    
    // Begin transaction
    $db->beginTransaction();
    
    // Update or create server if provided
    $serverId = null;
    if (!empty($data['server'])) {
        $serverData = [
            'server_name' => $data['server']['name'] ?? 'Unknown Server',
            'server_ip' => $data['server']['ip'] ?? '0.0.0.0',
            'server_port' => $data['server']['port'] ?? 0,
            'map_name' => $data['server']['map'] ?? null,
            'game_type' => $data['server']['gametype'] ?? null,
            'max_players' => $data['server']['max_players'] ?? 0,
            'current_players' => $data['server']['current_players'] ?? 0,
            'rounds_played' => 1
        ];
        $serverId = $serverClass->upsertServer($serverData);
    }
    
    // Update or create player
    $playerData = [
        'player_name' => $data['player']['name'],
        'player_hash' => $data['player']['hash'],
        'kills' => $data['player']['kills'] ?? 0,
        'deaths' => $data['player']['deaths'] ?? 0,
        'suicides' => $data['player']['suicides'] ?? 0,
        'teamkills' => $data['player']['teamkills'] ?? 0,
        'playtime' => $data['player']['playtime'] ?? 0,
        'rounds_played' => $data['player']['rounds'] ?? 1,
        'wins' => $data['player']['wins'] ?? 0,
        'losses' => $data['player']['losses'] ?? 0,
        'score' => $data['player']['score'] ?? 0,
        'headshots' => $data['player']['headshots'] ?? 0
    ];
    
    $playerId = $playerClass->upsertPlayer($playerData);
    
    // Update weapon stats if provided
    if (!empty($data['weapons']) && is_array($data['weapons'])) {
        foreach ($data['weapons'] as $weapon) {
            if (!empty($weapon['name'])) {
                $weaponStats = [
                    'kills' => $weapon['kills'] ?? 0,
                    'deaths' => $weapon['deaths'] ?? 0,
                    'shots' => $weapon['shots'] ?? 0,
                    'hits' => $weapon['hits'] ?? 0,
                    'headshots' => $weapon['headshots'] ?? 0
                ];
                $weaponClass->updateWeaponStats($playerId, $weapon['name'], $weaponStats);
            }
        }
    }
    
    // Commit transaction
    $db->commit();
    
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'player_id' => $playerId,
        'server_id' => $serverId,
        'message' => 'Statistics updated successfully'
    ]);
    
} catch (Exception $e) {
    // Rollback on error
    $db->rollback();
    
    error_log('Chronos API Error: ' . $e->getMessage());
    
    http_response_code(500);
    echo json_encode([
        'error' => 'Internal server error',
        'message' => 'Failed to process statistics'
    ]);
}
