<?php
// scratch/view_db.php
require_once __DIR__ . '/../includes/functions.php';

try {
    $stmt = $pdo->query("SELECT * FROM pendaftaran_tiket ORDER BY id_tiket DESC LIMIT 5");
    $records = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "LATEST BOOKINGS:\n";
    print_r($records);
} catch (PDOException $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
