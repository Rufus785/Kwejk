<?php
include("config.php");

// Sprawdzamy, czy użytkownik jest administratorem
if (isset($_SESSION['logged']) && $_SESSION['logged'] === true) {
    $user_id = $_SESSION['user_id'];
    $sql = "SELECT is_admin FROM users WHERE user_id = ? LIMIT 1";
    $stmt = $mysqli->prepare($sql);
    if ($stmt === false) {
        die('Błąd przygotowania zapytania: ' . $mysqli->error);
    }
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    if ($row['is_admin'] == 0) {
        die('Dostęp zabroniony');
    }
} else {
    die('Dostęp zabroniony');
}

// Sprawdzamy, czy zapytanie zostało wysłane
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['report_id']) && isset($_POST['status'])) {
    $report_id = $_POST['report_id'];
    $status = $_POST['status'];

    // Przygotowanie zmiennych do aktualizacji
    $resolved_by = $user_id;
    $resolved_at = date('Y-m-d H:i:s');
    
    if ($status == 1) {
        // False positive
        $update_sql = "UPDATE reports SET is_resolved = 1, resolved_by = ?, resolved_at = ? WHERE report_id = ?";
    } elseif ($status == 2) {
        // True positive
        $update_sql = "UPDATE reports SET is_resolved = 2, resolved_by = ?, resolved_at = ? WHERE report_id = ?";
        // Ukryj obrazek
        $update_image_sql = "UPDATE images SET is_deleted = 1 WHERE image_id = (SELECT image_id FROM reports WHERE report_id = ?)";
        $stmt_image = $mysqli->prepare($update_image_sql);
        $stmt_image->bind_param('i', $report_id);
        $stmt_image->execute();
    }
    
    // Aktualizujemy raport
    $stmt = $mysqli->prepare($update_sql);
    $stmt->bind_param('isi', $resolved_by, $resolved_at, $report_id);
    $stmt->execute();

    // Zwracamy komunikat o sukcesie
    echo 'OK';
} else {
    echo 'Błąd: Brak danych';
}
?>
