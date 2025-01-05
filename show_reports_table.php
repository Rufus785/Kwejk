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

// Pobranie parametru 'filter' z URL
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';

// Budowanie zapytania w zależności od filtra
if ($filter == 'unresolved') {
    $sql = "SELECT reports.report_id, images.caption, users.username, reports.created_at, reports.is_resolved, reports.reason
            FROM reports 
            JOIN images ON reports.image_id = images.image_id
            JOIN users ON reports.reported_by = users.user_id
            WHERE reports.is_resolved = 0
            ORDER BY reports.created_at DESC";
} elseif ($filter == 'false_positive') {
    $sql = "SELECT reports.report_id, images.caption, users.username, reports.created_at, reports.is_resolved, reports.reason
            FROM reports 
            JOIN images ON reports.image_id = images.image_id
            JOIN users ON reports.reported_by = users.user_id
            WHERE reports.is_resolved = 1
            ORDER BY reports.created_at DESC";
} elseif ($filter == 'true_positive') {
    $sql = "SELECT reports.report_id, images.caption, users.username, reports.created_at, reports.is_resolved, reports.reason
            FROM reports 
            JOIN images ON reports.image_id = images.image_id
            JOIN users ON reports.reported_by = users.user_id
            WHERE reports.is_resolved = 2
            ORDER BY reports.created_at DESC";
} else {
    $sql = "SELECT reports.report_id, images.caption, users.username, reports.created_at, reports.is_resolved, reports.reason
            FROM reports 
            JOIN images ON reports.image_id = images.image_id
            JOIN users ON reports.reported_by = users.user_id
            ORDER BY reports.created_at DESC";
}

// Wykonujemy zapytanie
$stmt = $mysqli->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();

// Wyświetlamy tabelę raportów
echo '<table border="1" style="width:100%; text-align:left;">';
echo '<tr><th>Kwejk</th><th>Login zgłaszającego</th><th>Data zgłoszenia</th><th>Status</th><th>Rozwiń</th></tr>';
while ($row = $result->fetch_assoc()) {
    echo '<tr>';
    echo '<td>' . htmlspecialchars($row['caption']) . '</td>';
    echo '<td>' . htmlspecialchars($row['username']) . '</td>';
    echo '<td>' . htmlspecialchars($row['created_at']) . '</td>';
    echo '<td>' . ($row['is_resolved'] == 0 ? 'Nierozwiązany' : ($row['is_resolved'] == 1 ? 'False Positive' : 'True Positive')) . '</td>';
    echo '<td><button onclick="toggleReportDetails(' . $row['report_id'] . ')">Rozwiń</button></td>';
    echo '</tr>';

    // Szczegóły raportu
    echo '<tr id="report-details-' . $row['report_id'] . '" style="display: none;">';
    echo '<td colspan="5" >';
    echo '<p><strong>Treść raportu:</strong> ' . htmlspecialchars($row['reason']) . '</p>';
    
    if ($row['is_resolved'] == 0) {
        // Opcje dla nierozwiązanych raportów
        echo '<button onclick="resolveReport(' . $row['report_id'] . ', 1)">Oznacz jako False Positive</button>';
        echo '<button onclick="resolveReport(' . $row['report_id'] . ', 2)">Oznacz jako True Positive</button>';
    } else {
        // Szczegóły dla rozwiązanych raportów
        echo '<p><strong>Status:</strong> ' . ($row['is_resolved'] == 1 ? 'False Positive' : 'True Positive') . '</p>';
        echo '<p><strong>Rozwiązane przez:</strong> ' . htmlspecialchars($row['resolved_by_username']) . '</p>';
        echo '<p><strong>Data rozwiązania:</strong> ' . htmlspecialchars($row['resolved_at']) . '</p>';
        if ($row['is_resolved'] == 2) {
            echo '<p><strong>Obrazek jest ukryty.</strong></p>';
        }
    }

    echo '</td>';
    echo '</tr>';
}
echo '</table>';
?>
