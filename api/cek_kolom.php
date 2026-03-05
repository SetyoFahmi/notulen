<?php
require_once 'config.php';

$columns = $pdo->query("PRAGMA table_info(notulen)")->fetchAll(PDO::FETCH_COLUMN, 1);
echo "<h3>Kolom dalam tabel notulen:</h3>";
echo "<ul>";
foreach ($columns as $col) {
    echo "<li>" . htmlspecialchars($col) . "</li>";
}
echo "</ul>";
?>