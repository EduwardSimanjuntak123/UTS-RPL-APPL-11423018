<?php
$password = 'password123';
$hash = password_hash($password, PASSWORD_BCRYPT);
echo "Bcrypt hash for 'password123':" . PHP_EOL;
echo $hash . PHP_EOL;
echo PHP_EOL;
echo "Hash length: " . strlen($hash) . PHP_EOL;
?>
