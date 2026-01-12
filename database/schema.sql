-- Chronos V3 Database Schema
-- Compatible with MariaDB 11 and MySQL 8+
-- Charset: utf8mb4 for full Unicode support

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: chronos_stats
--

-- --------------------------------------------------------

--
-- Table structure for table `players`
--

CREATE TABLE IF NOT EXISTS `players` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `player_name` varchar(100) NOT NULL,
  `player_hash` varchar(64) NOT NULL,
  `kills` int(11) DEFAULT 0,
  `deaths` int(11) DEFAULT 0,
  `suicides` int(11) DEFAULT 0,
  `teamkills` int(11) DEFAULT 0,
  `playtime` int(11) DEFAULT 0,
  `rounds_played` int(11) DEFAULT 0,
  `wins` int(11) DEFAULT 0,
  `losses` int(11) DEFAULT 0,
  `score` int(11) DEFAULT 0,
  `headshots` int(11) DEFAULT 0,
  `first_seen` datetime DEFAULT CURRENT_TIMESTAMP,
  `last_seen` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `player_hash` (`player_hash`),
  KEY `player_name` (`player_name`),
  KEY `kills` (`kills`),
  KEY `score` (`score`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `weapons`
--

CREATE TABLE IF NOT EXISTS `weapons` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `player_id` int(11) NOT NULL,
  `weapon_name` varchar(100) NOT NULL,
  `kills` int(11) DEFAULT 0,
  `deaths` int(11) DEFAULT 0,
  `shots` int(11) DEFAULT 0,
  `hits` int(11) DEFAULT 0,
  `headshots` int(11) DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `player_id` (`player_id`),
  KEY `weapon_name` (`weapon_name`),
  CONSTRAINT `weapons_player_fk` FOREIGN KEY (`player_id`) REFERENCES `players` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `servers`
--

CREATE TABLE IF NOT EXISTS `servers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `server_name` varchar(200) NOT NULL,
  `server_ip` varchar(45) NOT NULL,
  `server_port` int(11) DEFAULT 0,
  `map_name` varchar(100) DEFAULT NULL,
  `game_type` varchar(50) DEFAULT NULL,
  `max_players` int(11) DEFAULT 0,
  `current_players` int(11) DEFAULT 0,
  `rounds_played` int(11) DEFAULT 0,
  `first_seen` datetime DEFAULT CURRENT_TIMESTAMP,
  `last_seen` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `server_unique` (`server_ip`,`server_port`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `maps`
--

CREATE TABLE IF NOT EXISTS `maps` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `map_name` varchar(100) NOT NULL,
  `times_played` int(11) DEFAULT 0,
  `total_playtime` int(11) DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `map_name` (`map_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `rounds`
--

CREATE TABLE IF NOT EXISTS `rounds` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `server_id` int(11) NOT NULL,
  `map_id` int(11) NOT NULL,
  `round_start` datetime DEFAULT CURRENT_TIMESTAMP,
  `round_end` datetime DEFAULT NULL,
  `duration` int(11) DEFAULT 0,
  `winner_team` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `server_id` (`server_id`),
  KEY `map_id` (`map_id`),
  CONSTRAINT `rounds_server_fk` FOREIGN KEY (`server_id`) REFERENCES `servers` (`id`) ON DELETE CASCADE,
  CONSTRAINT `rounds_map_fk` FOREIGN KEY (`map_id`) REFERENCES `maps` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE IF NOT EXISTS `events` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `round_id` int(11) NOT NULL,
  `event_type` varchar(50) NOT NULL,
  `player_id` int(11) DEFAULT NULL,
  `target_id` int(11) DEFAULT NULL,
  `weapon_id` int(11) DEFAULT NULL,
  `event_time` datetime DEFAULT CURRENT_TIMESTAMP,
  `event_data` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `round_id` (`round_id`),
  KEY `player_id` (`player_id`),
  KEY `event_type` (`event_type`),
  CONSTRAINT `events_round_fk` FOREIGN KEY (`round_id`) REFERENCES `rounds` (`id`) ON DELETE CASCADE,
  CONSTRAINT `events_player_fk` FOREIGN KEY (`player_id`) REFERENCES `players` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Create views for common queries
--

CREATE OR REPLACE VIEW `player_rankings` AS
SELECT 
    p.id,
    p.player_name,
    p.kills,
    p.deaths,
    p.score,
    p.playtime,
    p.rounds_played,
    CASE WHEN p.deaths > 0 THEN ROUND(p.kills / p.deaths, 2) ELSE p.kills END AS kd_ratio,
    CASE WHEN p.rounds_played > 0 THEN ROUND(p.score / p.rounds_played, 2) ELSE 0 END AS avg_score
FROM players p
WHERE p.playtime >= 60
ORDER BY p.score DESC;

CREATE OR REPLACE VIEW `weapon_stats` AS
SELECT 
    w.weapon_name,
    SUM(w.kills) AS total_kills,
    SUM(w.deaths) AS total_deaths,
    SUM(w.hits) AS total_hits,
    SUM(w.shots) AS total_shots,
    SUM(w.headshots) AS total_headshots,
    CASE WHEN SUM(w.shots) > 0 THEN ROUND((SUM(w.hits) / SUM(w.shots)) * 100, 2) ELSE 0 END AS accuracy
FROM weapons w
GROUP BY w.weapon_name
ORDER BY total_kills DESC;
