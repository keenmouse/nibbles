<?php

function build_ipv6_data(string $ip): ?array
{
    $packed = @inet_pton($ip);
    if ($packed === false || strlen($packed) !== 16) {
        return null;
    }

    $hex = bin2hex($packed);
    $nibbles = str_split($hex);
    $nibblesI = implode('.', $nibbles);
    $nibblesIR = implode('.', array_reverse($nibbles));

    return [
        'ip' => $ip,
        'hex' => $hex,
        'nibbles_i' => $nibblesI,
        'nibbles_ir' => $nibblesIR,
        'spf_label' => $nibblesIR . '.ip6',
        'ip6_arpa' => $nibblesIR . '.ip6.arpa',
    ];
}

function wants_json(): bool
{
    if (isset($_GET['format']) && strtolower((string)$_GET['format']) === 'json') {
        return true;
    }

    $accept = $_SERVER['HTTP_ACCEPT'] ?? '';
    return stripos($accept, 'application/json') !== false;
}

function first_path_segment(): ?string
{
    $path = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH);
    if (!is_string($path)) {
        return null;
    }

    $path = trim($path, '/');
    if ($path === '') {
        return null;
    }

    $segments = preg_split('#/+#', $path);
    if (!is_array($segments)) {
        return null;
    }

    foreach ($segments as $segment) {
        if ($segment !== '') {
            return urldecode($segment);
        }
    }

    return null;
}

$pathInput = null;
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'GET') {
    $pathInput = first_path_segment();
}

if ($pathInput !== null) {
    $result = build_ipv6_data($pathInput);
    if ($result === null) {
        http_response_code(400);
        header('Content-Type: text/plain; charset=utf-8');
        echo "Invalid IPv6 address\n";
        exit;
    }

    if (wants_json()) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($result, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . "\n";
    } else {
        header('Content-Type: text/plain; charset=utf-8');
        echo $result['spf_label'] . "\n";
    }
    exit;
}

$postResult = null;
$postError = null;
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST' && isset($_POST['ipv6'])) {
    $input = trim((string)$_POST['ipv6']);
    $postResult = build_ipv6_data($input);
    if ($postResult === null) {
        $postError = 'Invalid IPv6 address.';
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IPv6 SPF/DNS Nibble Converter</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.4; margin: 2rem; }
        pre {
            background-color: #f4f4f4;
            padding: 10px;
            border: 1px solid #ddd;
            cursor: pointer;
            white-space: pre-wrap;
            word-break: break-all;
        }
        .hint { color: #555; font-size: 0.95rem; }
        .error { color: #b00020; font-weight: bold; }
    </style>
</head>
<body>
    <h2>IPv6 SPF/DNS Nibble Converter</h2>

    <form action="" method="post">
        <label for="ipv6">Enter IPv6 Address:</label>
        <input type="text" name="ipv6" id="ipv6" required>
        <input type="submit" value="Convert">
    </form>

    <p class="hint">Click any output block to copy it.</p>

    <?php if ($postError !== null): ?>
        <p class="error"><?php echo htmlspecialchars($postError, ENT_QUOTES, 'UTF-8'); ?></p>
    <?php elseif ($postResult !== null): ?>
        <h3>Original IPv6</h3>
        <pre class="copy-output"><?php echo htmlspecialchars($postResult['ip'], ENT_QUOTES, 'UTF-8'); ?></pre>

        <h3>%{i} (forward dotted nibbles)</h3>
        <pre class="copy-output"><?php echo htmlspecialchars($postResult['nibbles_i'], ENT_QUOTES, 'UTF-8'); ?></pre>

        <h3>%{ir} (reversed dotted nibbles)</h3>
        <pre class="copy-output"><?php echo htmlspecialchars($postResult['nibbles_ir'], ENT_QUOTES, 'UTF-8'); ?></pre>

        <h3>SPF label (%{ir}.%{v} where %{v}=ip6)</h3>
        <pre class="copy-output"><?php echo htmlspecialchars($postResult['spf_label'], ENT_QUOTES, 'UTF-8'); ?></pre>

        <h3>ip6.arpa convenience</h3>
        <pre class="copy-output"><?php echo htmlspecialchars($postResult['ip6_arpa'], ENT_QUOTES, 'UTF-8'); ?></pre>
    <?php endif; ?>

    <textarea id="clipboardTextarea" style="position:absolute;left:-9999px;top:-9999px;"></textarea>

    <script>
        function copyText(text) {
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(text).catch(function() {});
                return;
            }

            var textarea = document.getElementById('clipboardTextarea');
            textarea.value = text;
            textarea.select();
            try { document.execCommand('copy'); } catch (err) {}
        }

        document.querySelectorAll('.copy-output').forEach(function(node) {
            node.addEventListener('click', function() {
                copyText(node.innerText.trim());
            });
        });
    </script>
</body>
</html>
