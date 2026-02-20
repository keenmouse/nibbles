<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IPv6 to Full Nibble Format Converter</title>
    <style>
        pre {
            background-color: #f4f4f4;
            padding: 10px;
            border: 1px solid #ddd;
        }
    </style>
</head>
<body>
    <h2>IPv6 to Full Nibble Format Converter</h2>

    <form action="" method="post">
        <label for="ipv6">Enter IPv6 Address:</label>
        <input type="text" name="ipv6" id="ipv6" required>
        <input type="submit" value="Convert">
    </form>

    <?php
        function expandIPv6($ip) {
            $expandedIP = implode(':', array_map(function($block) {
                return str_pad($block, 4, '0', STR_PAD_LEFT);
            }, explode(':', inet_ntop(inet_pton($ip)))));

            $noColon = str_replace(':', '', $expandedIP);
            $nibbleFormat = implode('.', str_split($noColon));
            return $nibbleFormat;
        }

        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['ipv6'])) {
            $ipv6 = $_POST['ipv6'];
            echo "<h3>Original IPv6:</h3> <pre>$ipv6</pre>";
            $nibbleFormat = expandIPv6($ipv6);
            echo "<h3>Full Nibble Format:</h3> <pre id='nibbleOutput'>" . $nibbleFormat . "</pre>";
            echo "<button id='copyBtn'>Copy to Clipboard</button>";
        }
    ?>
    
    <!-- Hidden textarea for older browsers fallback -->
    <textarea id="clipboardTextarea" style="position: absolute; left: -9999px;"></textarea>

    <script>
        document.getElementById('copyBtn')?.addEventListener('click', function() {
            var nibbleText = document.getElementById('nibbleOutput').innerText;

            // Modern browsers Clipboard API
            if (navigator.clipboard) {
                navigator.clipboard.writeText(nibbleText).catch(function(err) {
                    console.error('Could not copy text to clipboard: ', err);
                });
            } else {
                // Fallback for older browsers
                var textarea = document.getElementById('clipboardTextarea');
                textarea.value = nibbleText;
                textarea.select();
                try {
                    document.execCommand('copy');
                } catch (err) {
                    console.error('Unable to copy', err);
                }
            }
        });
    </script>
</body>
</html>
