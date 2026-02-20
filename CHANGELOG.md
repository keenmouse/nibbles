# Changelog

## 0.1.1
- Added path-based IPv6 API mode (`GET /<ipv6>`) for SPF/DNS nibble workflows.
- Added plain-text default API output for `%{ir}.ip6` label generation.
- Added JSON API output (`Accept: application/json` or `?format=json`) with `%{i}`, `%{ir}`, and convenience fields.
- Reworked conversion logic to binary-safe IPv6 parsing (`inet_pton` + `bin2hex`) and nibble derivation.
- Updated HTML UI to show `%{i}`, `%{ir}`, SPF label, and `ip6.arpa`, with click-to-copy output blocks.
- Updated README documentation with curl examples and SPF macro mapping notes.

## 0.1.0
- Initial import from deployed `nibbles.keenmouse.com` source.
- Added project documentation and repo metadata.
