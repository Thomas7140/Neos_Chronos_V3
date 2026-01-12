# API Documentation

Chronos V3 provides a simple API for uploading game statistics from your BHD/BHDTS server.

## Authentication

All API requests require authentication using an API key.

Set your API key in `.env`:
```env
API_KEY=your_secure_random_key_here
```

Include the key in request headers:
```
X-API-Key: your_secure_random_key_here
```

## Endpoints

### Upload Stats

**POST** `/api/upload.php`

Upload player statistics from a game match.

#### Request Headers
```
Content-Type: application/json
X-API-Key: your_api_key
```

#### Request Body
```json
{
  "server_id": 1,
  "map_name": "df_kabul",
  "game_type": "tdm",
  "match_duration": 1800,
  "match_date": "2026-01-12 20:30:00",
  "players": [
    {
      "player_name": "Player1",
      "player_guid": "abc123def456",
      "kills": 25,
      "deaths": 10,
      "headshots": 5,
      "team_kills": 0,
      "score": 2500,
      "play_time": 1800,
      "weapons": [
        {
          "weapon_name": "M4A1",
          "kills": 15,
          "deaths": 5,
          "headshots": 3,
          "shots_fired": 200,
          "shots_hit": 50
        }
      ]
    }
  ]
}
```

#### Response
```json
{
  "success": true,
  "message": "Statistics uploaded successfully",
  "players_updated": 10
}
```

#### Error Response
```json
{
  "success": false,
  "error": "Invalid API key"
}
```

### Get Player Stats

**GET** `/api/player.php?guid={player_guid}`

Retrieve statistics for a specific player.

#### Parameters
- `guid` (required): Player GUID

#### Response
```json
{
  "success": true,
  "player": {
    "player_name": "Player1",
    "player_guid": "abc123def456",
    "kills": 1250,
    "deaths": 450,
    "headshots": 200,
    "rating": 5420,
    "games_played": 145,
    "last_seen": "2026-01-12 20:30:00"
  }
}
```

### Get Server Stats

**GET** `/api/server.php?id={server_id}`

Retrieve statistics for a specific server.

#### Parameters
- `id` (required): Server ID

#### Response
```json
{
  "success": true,
  "server": {
    "server_name": "My BHD Server",
    "total_players": 523,
    "total_kills": 45230,
    "total_deaths": 44980,
    "active_players_24h": 35
  }
}
```

## Rate Limiting

- 100 requests per hour per API key
- Burst limit: 10 requests per minute

## Error Codes

- `400` - Bad Request (invalid data)
- `401` - Unauthorized (invalid API key)
- `429` - Too Many Requests (rate limit exceeded)
- `500` - Internal Server Error

## Example Usage

### cURL
```bash
curl -X POST https://your-domain.com/api/upload.php \
  -H "Content-Type: application/json" \
  -H "X-API-Key: your_api_key" \
  -d @stats.json
```

### Python
```python
import requests
import json

url = "https://your-domain.com/api/upload.php"
headers = {
    "Content-Type": "application/json",
    "X-API-Key": "your_api_key"
}

data = {
    "server_id": 1,
    "map_name": "df_kabul",
    "players": [...]
}

response = requests.post(url, headers=headers, json=data)
print(response.json())
```

### PHP
```php
$url = "https://your-domain.com/api/upload.php";
$data = [
    "server_id" => 1,
    "map_name" => "df_kabul",
    "players" => [...]
];

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Content-Type: application/json",
    "X-API-Key: your_api_key"
]);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
curl_close($ch);

$result = json_decode($response, true);
print_r($result);
```

## Game Server Integration

To automatically upload stats from your BHD/BHDTS server:

1. Parse game log files after each match
2. Extract player statistics
3. Format as JSON according to API spec
4. POST to `/api/upload.php`

Example log parser locations:
- Windows: `C:\Program Files\NovaLogic\Black Hawk Down\logs\`
- Linux: `/opt/bhdserver/logs/`

## Webhooks (Coming Soon)

Future versions will support webhooks for real-time notifications:
- Player achievements
- New high scores
- Server milestones

## Support

For API issues or questions:
- GitHub Issues: https://github.com/Thomas7140/Neos_Chronos_V3/issues
- Include your API request/response in the issue
