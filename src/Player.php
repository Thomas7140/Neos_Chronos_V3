<?php
/**
 * Chronos V3 - Player Statistics Class
 * PHP 8+ implementation
 */

namespace Chronos;

class Player
{
    private Database $db;

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    /**
     * Get top players by score
     */
    public function getTopPlayers(int $limit = 50, int $offset = 0): array
    {
        $sql = "SELECT * FROM player_rankings LIMIT :limit OFFSET :offset";
        return $this->db->query($sql, ['limit' => $limit, 'offset' => $offset]);
    }

    /**
     * Get player by ID
     */
    public function getPlayerById(int $id): ?array
    {
        $sql = "SELECT * FROM players WHERE id = :id";
        return $this->db->queryOne($sql, ['id' => $id]);
    }

    /**
     * Get player by hash
     */
    public function getPlayerByHash(string $hash): ?array
    {
        $sql = "SELECT * FROM players WHERE player_hash = :hash";
        return $this->db->queryOne($sql, ['hash' => $hash]);
    }

    /**
     * Search players by name
     */
    public function searchPlayers(string $name, int $limit = 50): array
    {
        $sql = "SELECT * FROM players WHERE player_name LIKE :name ORDER BY score DESC LIMIT :limit";
        return $this->db->query($sql, ['name' => '%' . $name . '%', 'limit' => $limit]);
    }

    /**
     * Get player weapon statistics
     */
    public function getPlayerWeapons(int $playerId): array
    {
        $sql = "SELECT * FROM weapons WHERE player_id = :player_id ORDER BY kills DESC";
        return $this->db->query($sql, ['player_id' => $playerId]);
    }

    /**
     * Create or update player
     */
    public function upsertPlayer(array $data): int
    {
        $existing = $this->getPlayerByHash($data['player_hash']);
        
        if ($existing) {
            // Update existing player
            $sql = "UPDATE players SET 
                    player_name = :name,
                    kills = kills + :kills,
                    deaths = deaths + :deaths,
                    suicides = suicides + :suicides,
                    teamkills = teamkills + :teamkills,
                    playtime = playtime + :playtime,
                    rounds_played = rounds_played + :rounds,
                    wins = wins + :wins,
                    losses = losses + :losses,
                    score = score + :score,
                    headshots = headshots + :headshots,
                    last_seen = NOW()
                    WHERE player_hash = :hash";
            
            $this->db->execute($sql, [
                'name' => $data['player_name'],
                'kills' => $data['kills'] ?? 0,
                'deaths' => $data['deaths'] ?? 0,
                'suicides' => $data['suicides'] ?? 0,
                'teamkills' => $data['teamkills'] ?? 0,
                'playtime' => $data['playtime'] ?? 0,
                'rounds' => $data['rounds_played'] ?? 0,
                'wins' => $data['wins'] ?? 0,
                'losses' => $data['losses'] ?? 0,
                'score' => $data['score'] ?? 0,
                'headshots' => $data['headshots'] ?? 0,
                'hash' => $data['player_hash']
            ]);
            
            return $existing['id'];
        } else {
            // Insert new player
            $sql = "INSERT INTO players (
                    player_name, player_hash, kills, deaths, suicides, teamkills,
                    playtime, rounds_played, wins, losses, score, headshots
                    ) VALUES (
                    :name, :hash, :kills, :deaths, :suicides, :teamkills,
                    :playtime, :rounds, :wins, :losses, :score, :headshots
                    )";
            
            $this->db->execute($sql, [
                'name' => $data['player_name'],
                'hash' => $data['player_hash'],
                'kills' => $data['kills'] ?? 0,
                'deaths' => $data['deaths'] ?? 0,
                'suicides' => $data['suicides'] ?? 0,
                'teamkills' => $data['teamkills'] ?? 0,
                'playtime' => $data['playtime'] ?? 0,
                'rounds' => $data['rounds_played'] ?? 0,
                'wins' => $data['wins'] ?? 0,
                'losses' => $data['losses'] ?? 0,
                'score' => $data['score'] ?? 0,
                'headshots' => $data['headshots'] ?? 0
            ]);
            
            return (int)$this->db->lastInsertId();
        }
    }

    /**
     * Get total player count
     */
    public function getTotalPlayers(): int
    {
        $result = $this->db->queryOne("SELECT COUNT(*) as count FROM players");
        return $result ? (int)$result['count'] : 0;
    }

    /**
     * Calculate K/D ratio
     */
    public static function calculateKDRatio(int $kills, int $deaths): float
    {
        return $deaths > 0 ? round($kills / $deaths, 2) : (float)$kills;
    }
}
