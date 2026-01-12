<?php
/**
 * Chronos V3 - Weapon Statistics Class
 * PHP 8+ implementation
 */

namespace Chronos;

class Weapon
{
    private Database $db;

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    /**
     * Get overall weapon statistics
     */
    public function getWeaponStats(int $limit = 50): array
    {
        $sql = "SELECT * FROM weapon_stats LIMIT :limit";
        return $this->db->query($sql, ['limit' => $limit]);
    }

    /**
     * Get weapon statistics for specific player
     */
    public function getPlayerWeaponStats(int $playerId): array
    {
        $sql = "SELECT 
                w.*,
                CASE WHEN w.shots > 0 THEN ROUND((w.hits / w.shots) * 100, 2) ELSE 0 END as accuracy
                FROM weapons w
                WHERE w.player_id = :player_id
                ORDER BY w.kills DESC";
        return $this->db->query($sql, ['player_id' => $playerId]);
    }

    /**
     * Update weapon statistics for a player
     */
    public function updateWeaponStats(int $playerId, string $weaponName, array $stats): void
    {
        $existing = $this->db->queryOne(
            "SELECT * FROM weapons WHERE player_id = :player_id AND weapon_name = :weapon",
            ['player_id' => $playerId, 'weapon' => $weaponName]
        );

        if ($existing) {
            $sql = "UPDATE weapons SET
                    kills = kills + :kills,
                    deaths = deaths + :deaths,
                    shots = shots + :shots,
                    hits = hits + :hits,
                    headshots = headshots + :headshots
                    WHERE player_id = :player_id AND weapon_name = :weapon";
        } else {
            $sql = "INSERT INTO weapons (player_id, weapon_name, kills, deaths, shots, hits, headshots)
                    VALUES (:player_id, :weapon, :kills, :deaths, :shots, :hits, :headshots)";
        }

        $this->db->execute($sql, [
            'player_id' => $playerId,
            'weapon' => $weaponName,
            'kills' => $stats['kills'] ?? 0,
            'deaths' => $stats['deaths'] ?? 0,
            'shots' => $stats['shots'] ?? 0,
            'hits' => $stats['hits'] ?? 0,
            'headshots' => $stats['headshots'] ?? 0
        ]);
    }

    /**
     * Calculate weapon accuracy
     */
    public static function calculateAccuracy(int $hits, int $shots): float
    {
        return $shots > 0 ? round(($hits / $shots) * 100, 2) : 0.0;
    }
}
