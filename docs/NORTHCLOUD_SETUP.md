# North Cloud Article Feed Setup

Diidjaaheer consumes articles from North Cloud via Redis pub/sub. This document describes the configuration and deployment.

## Channels

Set `NORTHCLOUD_CHANNELS` in production `.env` to subscribe to Layer 1 (topic) and Layer 4/5 (location) channels:

```
NORTHCLOUD_CHANNELS=articles:news,articles:local,mining:canada,crime:canada,entertainment:homepage
```

- `articles:news` - General news
- `articles:local` - Local/regional news
- `mining:canada` - Canadian mining content
- `crime:canada` - Canadian crime coverage
- `entertainment:homepage` - Core entertainment

## Redis Connection

Ensure `NORTHCLOUD_REDIS_CONNECTION` points to the same Redis instance the North Cloud publisher uses. The `northcloud` connection in `config/database.php` uses the same host/port as default Redis; override with `REDIS_HOST` etc. for production.

## Article Subscription Service

The `diidjaaheer-articles-subscribe.service` systemd unit runs `php artisan articles:subscribe` continuously. It is installed and started by the deploy process.

## Production Checklist

1. Set `NORTHCLOUD_CHANNELS` in shared `.env` on the server
2. Ensure Redis connectivity to North Cloud's Redis instance
3. Deploy — the articles-subscribe service starts automatically
4. Verify: `systemctl --user status diidjaaheer-articles-subscribe.service`

## Sources (North Cloud)

Canadian/North American sources were added via North Cloud MCP/API. The publisher auto-discovers `*_classified_content` indexes; no additional publisher configuration is needed.
