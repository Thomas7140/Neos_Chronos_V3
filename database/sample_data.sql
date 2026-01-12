-- Sample data for testing Chronos V3
-- This file provides sample statistics data for development/testing

-- Insert sample servers
INSERT INTO `servers` (`server_name`, `server_ip`, `server_port`, `map_name`, `game_type`, `max_players`, `current_players`, `rounds_played`) VALUES
('Elite BHD Server #1', '192.168.1.100', 17300, 'City Streets', 'Team Deathmatch', 32, 24, 150),
('Tactical Gaming Server', '192.168.1.101', 17301, 'Desert Compound', 'Capture the Flag', 24, 18, 89),
('No Mercy BHD TS', '192.168.1.102', 17302, 'Urban Warfare', 'Team Sabre', 16, 12, 45);

-- Insert sample maps
INSERT INTO `maps` (`map_name`, `times_played`, `total_playtime`) VALUES
('City Streets', 150, 45000),
('Desert Compound', 89, 26700),
('Urban Warfare', 45, 13500),
('Riverside', 67, 20100),
('Mountain Pass', 34, 10200);

-- Insert sample players
INSERT INTO `players` (`player_name`, `player_hash`, `kills`, `deaths`, `suicides`, `teamkills`, `playtime`, `rounds_played`, `wins`, `losses`, `score`, `headshots`) VALUES
('SnipeKing', 'hash_001', 1520, 780, 15, 8, 36000, 145, 89, 56, 152000, 456),
('TacticalNinja', 'hash_002', 1435, 820, 12, 5, 34200, 138, 82, 56, 143500, 401),
('Rambo2000', 'hash_003', 1398, 901, 20, 12, 32400, 132, 75, 57, 139800, 378),
('SilentKiller', 'hash_004', 1256, 654, 8, 3, 28800, 118, 71, 47, 125600, 512),
('StormTrooper', 'hash_005', 1187, 945, 18, 15, 27000, 115, 62, 53, 118700, 298),
('GhostRider', 'hash_006', 1089, 723, 10, 6, 24600, 102, 58, 44, 108900, 367),
('DeadEye', 'hash_007', 1045, 678, 7, 4, 23400, 98, 55, 43, 104500, 421),
('BulletStorm', 'hash_008', 987, 812, 14, 9, 22200, 94, 51, 43, 98700, 289),
('NightHawk', 'hash_009', 945, 701, 11, 7, 21000, 89, 48, 41, 94500, 334),
('WarMachine', 'hash_010', 923, 734, 13, 8, 20400, 87, 46, 41, 92300, 301),
('Phantom', 'hash_011', 876, 689, 9, 5, 19200, 82, 44, 38, 87600, 298),
('Destroyer', 'hash_012', 834, 712, 12, 10, 18000, 78, 41, 37, 83400, 267),
('Predator', 'hash_013', 798, 654, 8, 6, 17400, 75, 39, 36, 79800, 289),
('Assassin', 'hash_014', 756, 623, 7, 4, 16200, 71, 37, 34, 75600, 312),
('Viper', 'hash_015', 712, 689, 11, 8, 15600, 68, 34, 34, 71200, 245);

-- Insert sample weapon statistics for top players
INSERT INTO `weapons` (`player_id`, `weapon_name`, `kills`, `deaths`, `shots`, `hits`, `headshots`) VALUES
-- Player 1 (SnipeKing) - favors sniper rifles
(1, 'M24 Sniper Rifle', 789, 234, 1200, 950, 234),
(1, 'M4A1 Carbine', 456, 312, 8900, 3456, 145),
(1, 'M9 Pistol', 275, 234, 2400, 1200, 77),
-- Player 2 (TacticalNinja) - balanced loadout
(2, 'M4A1 Carbine', 678, 389, 12000, 4234, 189),
(2, 'MP5 SMG', 445, 267, 8900, 3567, 123),
(2, 'M9 Pistol', 312, 164, 2100, 987, 89),
-- Player 3 (Rambo2000) - aggressive style
(3, 'SAW Machine Gun', 834, 512, 45000, 12000, 178),
(3, 'M4A1 Carbine', 389, 234, 7800, 2890, 134),
(3, 'M67 Grenade', 175, 155, 234, 189, 66),
-- Player 4 (SilentKiller) - precision shooter
(4, 'M24 Sniper Rifle', 945, 312, 1400, 1200, 378),
(4, 'M4A1 Carbine', 234, 198, 4500, 1890, 98),
(4, 'M9 Pistol', 77, 144, 890, 456, 36),
-- Player 5 (StormTrooper) - spray and pray
(5, 'SAW Machine Gun', 723, 567, 67000, 15000, 189),
(5, 'MP5 SMG', 345, 234, 12000, 4567, 78),
(5, 'M9 Pistol', 119, 144, 1200, 567, 31);

-- Insert sample rounds
INSERT INTO `rounds` (`server_id`, `map_id`, `round_start`, `round_end`, `duration`, `winner_team`) VALUES
(1, 1, '2026-01-10 14:30:00', '2026-01-10 14:50:00', 1200, 'Team A'),
(1, 1, '2026-01-10 15:00:00', '2026-01-10 15:18:00', 1080, 'Team B'),
(2, 2, '2026-01-10 16:00:00', '2026-01-10 16:22:00', 1320, 'Team A'),
(2, 3, '2026-01-10 17:00:00', '2026-01-10 17:25:00', 1500, 'Team B'),
(3, 4, '2026-01-10 18:00:00', '2026-01-10 18:20:00', 1200, 'Team A');

-- Update last_seen timestamps to recent
UPDATE `players` SET `last_seen` = NOW() - INTERVAL FLOOR(RAND() * 24) HOUR;
UPDATE `servers` SET `last_seen` = NOW() - INTERVAL FLOOR(RAND() * 6) HOUR;
