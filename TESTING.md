# Testing

This project supports two usage modes:
- HTML form (`GET /`, `POST /`)
- Path-based API (`GET /<ipv6>`)

## Local test setup

Start local PHP server from repo root:

```bash
php -S 127.0.0.1:8000
```

## Syntax check

```bash
php -l index.php
```

Pass condition:
- `No syntax errors detected in index.php`

## API tests

### 1) Plain output (`%{ir}.ip6`)

```bash
curl -sS "http://127.0.0.1:8000/2001:db8::1"
```

Expected:
- plain text ending in `.ip6`
- expected sample:
  `1.0.0.0.0.0.0.0.0.0.0.0.0.0.0.0.0.0.0.0.0.0.0.0.8.b.d.0.1.0.0.2.ip6`

### 2) JSON output

```bash
curl -sS -H "Accept: application/json" "http://127.0.0.1:8000/2001:db8::1"
```

Expected keys:
- `ip`
- `hex`
- `nibbles_i`
- `nibbles_ir`
- `spf_label`
- `ip6_arpa`

Expected values for sample:
- `ip` = `2001:db8::1`
- `hex` = `20010db8000000000000000000000001`
- `spf_label` ends with `.ip6`
- `ip6_arpa` ends with `.ip6.arpa`

### 3) Invalid IPv6 path

```bash
curl -i -sS "http://127.0.0.1:8000/not-an-ipv6"
```

Expected:
- HTTP `400`
- body contains `Invalid IPv6 address`

## Production smoke checks (optional)

```bash
curl -sS "https://nibbles.keenmouse.com/2001:db8::1"
curl -sS -H "Accept: application/json" "https://nibbles.keenmouse.com/2001:db8::1"
curl -i -sS "https://nibbles.keenmouse.com/not-an-ipv6"
```

Pass criteria match local API expectations.
