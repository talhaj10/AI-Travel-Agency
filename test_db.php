<?php
try {
    $pdo = new PDO("mysql:host=127.0.0.1;port=3307", "root", "");
    echo "SUCCESS: Connected to MySQL at 127.0.0.1:3307\n";
} catch (PDOException $e) {
    echo "ERROR (127.0.0.1:3307): " . $e->getMessage() . "\n";
}

try {
    $pdo = new PDO("mysql:host=localhost;port=3307", "root", "");
    echo "SUCCESS: Connected to MySQL at localhost:3307\n";
} catch (PDOException $e) {
    echo "ERROR (localhost:3307): " . $e->getMessage() . "\n";
}
