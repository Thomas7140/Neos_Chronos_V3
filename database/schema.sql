-- Chronos V3 Database Schema
-- Version 3.0.0
-- Compatible with MySQL 5.7+ and MariaDB 10.3+

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

-- Create players table
CREATE TABLE IF NOT EXISTS `chronos_players` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `player_name` varchar(100) NOT NULL,
  `player_guid` varchar(64) NOT NULL,
  `kills` int(11) NOT NULL DEFAULT 0,
  `deaths` int(11) NOT NULL DEFAULT 0,
  `headshots` int(11) NOT NULL DEFAULT 0,
  `team_kills` int(11) NOT NULL DEFAULT 0,
  `rating` int(11) NOT NULL DEFAULT 0,
  `play_time` int(11) NOT NULL DEFAULT 0,
  `games_played` int(11) NOT NULL DEFAULT 0,
  `wins` int(11) NOT NULL DEFAULT 0,
  `losses` int(11) NOT NULL DEFAULT 0,
  `first_seen` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `last_seen` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `country_code` varchar(2) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `player_guid` (`player_guid`),
  KEY `player_name` (`player_name`),
  KEY `rating` (`rating`),
  KEY `kills` (`kills`),
  KEY `last_seen` (`last_seen`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create weapons table
CREATE TABLE IF NOT EXISTS `chronos_weapons` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `player_id` int(11) NOT NULL,
  `weapon_name` varchar(100) NOT NULL,
  `kills` int(11) NOT NULL DEFAULT 0,
  `deaths` int(11) NOT NULL DEFAULT 0,
  `headshots` int(11) NOT NULL DEFAULT 0,
  `shots_fired` int(11) NOT NULL DEFAULT 0,
  `shots_hit` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `player_id` (`player_id`),
  KEY `weapon_name` (`weapon_name`),
  FOREIGN KEY (`player_id`) REFERENCES `chronos_players` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create maps table
CREATE TABLE IF NOT EXISTS `chronos_maps` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `player_id` int(11) NOT NULL,
  `map_name` varchar(100) NOT NULL,
  `kills` int(11) NOT NULL DEFAULT 0,
  `deaths` int(11) NOT NULL DEFAULT 0,
  `wins` int(11) NOT NULL DEFAULT 0,
  `losses` int(11) NOT NULL DEFAULT 0,
  `play_time` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `player_id` (`player_id`),
  KEY `map_name` (`map_name`),
  FOREIGN KEY (`player_id`) REFERENCES `chronos_players` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create awards table
CREATE TABLE IF NOT EXISTS `chronos_awards` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `award_name` varchar(100) NOT NULL,
  `award_description` text,
  `award_icon` varchar(255) DEFAULT NULL,
  `award_type` varchar(50) NOT NULL,
  `award_criteria` text,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `award_type` (`award_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create player_awards table
CREATE TABLE IF NOT EXISTS `chronos_player_awards` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `player_id` int(11) NOT NULL,
  `award_id` int(11) NOT NULL,
  `earned_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `times_earned` int(11) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `player_id` (`player_id`),
  KEY `award_id` (`award_id`),
  FOREIGN KEY (`player_id`) REFERENCES `chronos_players` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`award_id`) REFERENCES `chronos_awards` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create ranks table
CREATE TABLE IF NOT EXISTS `chronos_ranks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rank_name` varchar(100) NOT NULL,
  `min_rating` int(11) NOT NULL DEFAULT 0,
  `rank_icon` varchar(255) DEFAULT NULL,
  `rank_order` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `min_rating` (`min_rating`),
  KEY `rank_order` (`rank_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create servers table
CREATE TABLE IF NOT EXISTS `chronos_servers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `server_name` varchar(100) NOT NULL,
  `server_ip` varchar(45) NOT NULL,
  `server_port` int(11) NOT NULL DEFAULT 3000,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `last_update` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `is_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create stats table for detailed match statistics
CREATE TABLE IF NOT EXISTS `chronos_stats` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `player_id` int(11) NOT NULL,
  `server_id` int(11) NOT NULL,
  `map_name` varchar(100) NOT NULL,
  `game_type` varchar(50) NOT NULL,
  `kills` int(11) NOT NULL DEFAULT 0,
  `deaths` int(11) NOT NULL DEFAULT 0,
  `headshots` int(11) NOT NULL DEFAULT 0,
  `team_kills` int(11) NOT NULL DEFAULT 0,
  `score` int(11) NOT NULL DEFAULT 0,
  `play_time` int(11) NOT NULL DEFAULT 0,
  `match_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `player_id` (`player_id`),
  KEY `server_id` (`server_id`),
  KEY `match_date` (`match_date`),
  FOREIGN KEY (`player_id`) REFERENCES `chronos_players` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`server_id`) REFERENCES `chronos_servers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create monthly stats table
CREATE TABLE IF NOT EXISTS `chronos_monthly_stats` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `player_id` int(11) NOT NULL,
  `month` int(2) NOT NULL,
  `year` int(4) NOT NULL,
  `kills` int(11) NOT NULL DEFAULT 0,
  `deaths` int(11) NOT NULL DEFAULT 0,
  `headshots` int(11) NOT NULL DEFAULT 0,
  `rating` int(11) NOT NULL DEFAULT 0,
  `games_played` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `player_id` (`player_id`),
  KEY `month_year` (`month`, `year`),
  FOREIGN KEY (`player_id`) REFERENCES `chronos_players` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create sessions table
CREATE TABLE IF NOT EXISTS `chronos_sessions` (
  `id` varchar(128) NOT NULL,
  `data` text,
  `last_activity` int(11) NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `last_activity` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create admin table
CREATE TABLE IF NOT EXISTS `chronos_admin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `last_login` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default ranks
INSERT INTO `chronos_ranks` (`rank_name`, `min_rating`, `rank_icon`, `rank_order`) VALUES
('Recruit', 0, 'rank_0.png', 0),
('Private', 100, 'rank_1.png', 1),
('Corporal', 250, 'rank_2.png', 2),
('Sergeant', 500, 'rank_3.png', 3),
('Staff Sergeant', 1000, 'rank_4.png', 4),
('Lieutenant', 2000, 'rank_5.png', 5),
('Captain', 3500, 'rank_6.png', 6),
('Major', 5000, 'rank_7.png', 7),
('Colonel', 7500, 'rank_8.png', 8),
('General', 10000, 'rank_9.png', 9);

-- Insert default awards
INSERT INTO `chronos_awards` (`award_name`, `award_description`, `award_icon`, `award_type`, `award_criteria`) VALUES
('Sharpshooter', 'Awarded for 100+ headshots', 'award_sharpshooter.png', 'kills', 'headshots >= 100'),
('Survivor', 'Awarded for K/D ratio above 2.0', 'award_survivor.png', 'ratio', 'kd_ratio >= 2.0'),
('Veteran', 'Awarded for 100+ games played', 'award_veteran.png', 'games', 'games_played >= 100'),
('Headhunter', 'Awarded for 50+ headshots in a month', 'award_headhunter.png', 'monthly', 'monthly_headshots >= 50'),
('Killer', 'Awarded for 1000+ total kills', 'award_killer.png', 'kills', 'kills >= 1000'),
('Elite', 'Awarded for 5000+ rating', 'award_elite.png', 'rating', 'rating >= 5000');

-- Insert default admin user (password: admin123 - CHANGE THIS!)
-- Password hash for "admin123" using PASSWORD_ARGON2ID
INSERT INTO `chronos_admin` (`username`, `password`, `email`) VALUES
('admin', '$argon2id$v=19$m=65536,t=4,p=1$OEJaZ3dPWThiQWI5RjdaQw$gWXs1hLKLjL0e9VhqF5qF8+7FZxQBgC9j6vLUYqF1pU', 'admin@example.com');
