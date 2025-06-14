<?php
if (extension_loaded('pdo_sqlite')) {
    echo "PDO SQLite extension is loaded and enabled for CLI.\n";
} else {
    echo "PDO SQLite extension is NOT loaded or enabled for CLI. Please enable it in your php.ini for CLI.\n";
}
?>