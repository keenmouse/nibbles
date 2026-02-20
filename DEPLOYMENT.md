# Deployment

This document describes web-server requirements for `index.php` path API mode.

## Required behavior

The app supports:
- HTML mode at `/`
- API mode at `/<ipv6>`

For API mode to work, the vhost must route non-file/non-directory paths to `index.php` (front-controller pattern), while leaving `/.well-known/` untouched.

## Apache vhost requirements

1. Ensure rewrite engine is enabled in the target vhost:
- `RewriteEngine On`

2. Add front-controller rules (example for this site):
- host constraint for `nibbles.keenmouse.com` (and optional `www`)
- bypass `/.well-known/`
- do not rewrite existing files/directories
- rewrite remaining paths to `/index.php`

Reference snippet:
- `deploy/apache/nibbles-path-api.conf.snippet`

3. Ensure PHP handler applies to `index.php`:
- `FilesMatch \\.php$` -> php-fpm handler for this vhost

## Validation checklist

After deployment/reload:

```bash
apachectl -t
systemctl reload apache2
```

Then verify endpoint behavior:

```bash
curl -i -sS "https://nibbles.keenmouse.com/2001:db8::1"
curl -i -sS -H "Accept: application/json" "https://nibbles.keenmouse.com/2001:db8::1"
curl -i -sS "https://nibbles.keenmouse.com/not-an-ipv6"
```

Expected:
- valid path -> HTTP `200`
- JSON request -> `Content-Type: application/json`
- invalid path input -> HTTP `400` with `Invalid IPv6 address`

## Rollback

1. Restore prior vhost config backup.
2. Run:
```bash
apachectl -t
systemctl reload apache2
```
3. Re-test `/` and path endpoint behavior.

## Notes

- Path API uses URL path segment parsing in PHP:
  - first non-empty segment from `REQUEST_URI`
  - `urldecode()` applied before IPv6 validation
- If vhost rewrite rules are missing or blocked, requests return web-server `404/403` before PHP executes.
