# Nibbles

Small PHP utility used at `nibbles.keenmouse.com` to convert IPv6 addresses into fully expanded nibble format.

## Purpose
- Accept an IPv6 address input.
- Expand/compress handling via `inet_pton`/`inet_ntop`.
- Output dotted nibble sequence (one hex character per label), with copy-to-clipboard support.

This output is useful for DNS record workflows that require nibble-expanded IPv6 notation, including the DNS-lookup-based SPF method used in this environment (for expressing larger authorized sender sets through DNS indirection rather than a single static SPF string).

## Runtime
- PHP web app (single entry file: `index.php`)
- Static icon assets in `icon/`

## Deployment origin
- Source copied from: `wagner:/home/nibbles/public_html`
- Local project path: `<local-path>`

