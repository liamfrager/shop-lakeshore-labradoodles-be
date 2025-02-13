<?php
function loadEnv($file) {
    if (file_exists($file)) {
        $lines = file($file);
        foreach ($lines as $line) {
            // Remove spaces and newlines, and ignore empty lines and comments
            $line = trim($line);
            if (empty($line) || $line[0] === '#') continue;

            // Split into key-value pairs
            list($key, $value) = explode('=', $line, 2);
            putenv(trim($key) . '=' . trim($value));  // Set the environment variable
        }
    }
}

// Load the environment variables
loadEnv(__DIR__ . '/.env');
?>