# Nibbles

Small PHP utility for IPv6 nibble conversion used in DNS/SPF workflows.

## Purpose
- Accept an IPv6 address.
- Convert using binary-safe parsing (`inet_pton` + `bin2hex`).
- Produce:
  - `%{i}` form for IPv6: forward dotted nibbles
  - `%{ir}` form: reversed dotted nibbles
  - SPF label convenience: `%{ir}.%{v}` where `%{v}` is `ip6`

This is designed for DNS-lookup-based SPF macro workflows where a practical label component is often `%{ir}.ip6` before appending your domain suffix.

## API endpoint (path mode)
Request format:
- `GET /<ipv6>`
- encoded colons are supported, e.g. `/2001%3Adb8%3A%3A1`

### Plain output (default)
```bash
curl "https://nibbles.keenmouse.com/2001:db8::1"
```
Returns plain text:
- `{reversed_nibbles}.ip6`

### JSON output
By `Accept` header:
```bash
curl -H "Accept: application/json" "https://nibbles.keenmouse.com/2001:db8::1"
```
Or query arg:
```bash
curl "https://nibbles.keenmouse.com/2001:db8::1?format=json"
```
Returns fields:
- `ip`
- `hex`
- `nibbles_i` (`%{i}`)
- `nibbles_ir` (`%{ir}`)
- `spf_label` (`{ir}.ip6`)
- `ip6_arpa` (`{ir}.ip6.arpa`)

### Invalid input
- Path mode invalid IPv6 returns HTTP `400` and plain-text `Invalid IPv6 address`.

## HTML UI mode
- Existing form UI is kept for interactive use.
- POST output includes:
  - original IPv6
  - `%{i}`
  - `%{ir}`
  - SPF label (`{ir}.ip6`)
  - `ip6.arpa`
- Click any output block to copy to clipboard.

## Runtime
- PHP app (`index.php`)
- Static icon assets in `icon/`
