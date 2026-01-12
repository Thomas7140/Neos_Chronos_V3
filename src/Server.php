<?php
/**
 * Chronos V3 - Server Statistics Class
 * PHP 8+ implementation
 */

namespace Chronos;

class Server
{
    private Database $db;

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    /**
     * Get all servers
     */
    public function getAllServers(): array
    {
        $sql = "SELECT * FROM servers ORDER BY last_seen DESC";
        return $this->db->query($sql);
    }

    /**
     * Get server by ID
     */
    public function getServerById(int $id): ?array
    {
        $sql = "SELECT * FROM servers WHERE id = :id";
        return $this->db->queryOne($sql, ['id' => $id]);
    }

    /**
     * Create or update server
     */
    public function upsertServer(array $data): int
    {
        $existing = $this->db->queryOne(
            "SELECT * FROM servers WHERE server_ip = :ip AND server_port = :port",
            ['ip' => $data['server_ip'], 'port' => $data['server_port']]
        );

        if ($existing) {
            $sql = "UPDATE servers SET
                    server_name = :name,
                    map_name = :map,
                    game_type = :game_type,
                    max_players = :max_players,
                    current_players = :current_players,
                    rounds_played = rounds_played + :rounds,
                    last_seen = NOW()
                    WHERE server_ip = :ip AND server_port = :port";
            
            $this->db->execute($sql, [
                'name' => $data['server_name'],
                'map' => $data['map_name'] ?? null,
                'game_type' => $data['game_type'] ?? null,
                'max_players' => $data['max_players'] ?? 0,
                'current_players' => $data['current_players'] ?? 0,
                'rounds' => $data['rounds_played'] ?? 0,
                'ip' => $data['server_ip'],
                'port' => $data['server_port']
            ]);
            
            return $existing['id'];
        } else {
            $sql = "INSERT INTO servers (
                    server_name, server_ip, server_port, map_name, game_type,
                    max_players, current_players, rounds_played
                    ) VALUES (
                    :name, :ip, :port, :map, :game_type,
                    :max_players, :current_players, :rounds
                    )";
            
            $this->db->execute($sql, [
                'name' => $data['server_name'],
                'ip' => $data['server_ip'],
                'port' => $data['server_port'],
                'map' => $data['map_name'] ?? null,
                'game_type' => $data['game_type'] ?? null,
                'max_players' => $data['max_players'] ?? 0,
                'current_players' => $data['current_players'] ?? 0,
                'rounds' => $data['rounds_played'] ?? 0
            ]);
            
            return (int)$this->db->lastInsertId();
        }
    }

    /**
     * Get most played maps
     */
    public function getPopularMaps(int $limit = 20): array
    {
        $sql = "SELECT * FROM maps ORDER BY times_played DESC LIMIT :limit";
        return $this->db->query($sql, ['limit' => $limit]);
    }
}
