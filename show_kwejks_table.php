<?php
include("config.php"); // Łączymy się z bazą danych (zakładając, że config.php już wywołuje session_start)

// Sprawdzamy, czy użytkownik jest zalogowany
if (!isset($_SESSION['logged']) || $_SESSION['logged'] !== true) {
    echo 'Musisz być zalogowany, aby zobaczyć tę stronę.';
    exit();
}

// Sprawdzamy, czy użytkownik jest administratorem
$user_id = $_SESSION['user_id'];
$sql = "SELECT is_admin FROM users WHERE user_id = ? LIMIT 1";
$stmt = $mysqli->prepare($sql);
if ($stmt === false) {
    die('Błąd w przygotowaniu zapytania: ' . $mysqli->error);
}

$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

// Jeżeli użytkownik nie jest adminem, wyświetlamy komunikat
if ($row['is_admin'] != 1) {
    echo 'Nie masz uprawnień administratora, aby zobaczyć tę stronę.';
    exit();
}

// Pobieramy dane kwejków z bazy
$sql = "SELECT images.image_id, images.caption, images.is_deleted, images.created_at, users.username 
        FROM images
        JOIN users ON images.user_id = users.user_id
        ORDER BY images.created_at DESC";
$result = $mysqli->query($sql);

// Jeżeli zapytanie nie powiodło się, wyświetlamy błąd
if ($result === false) {
    echo 'Błąd zapytania: ' . $mysqli->error;
    exit();
}

// Jeśli dane są dostępne, tworzymy tabelę
if ($result->num_rows > 0) {
    echo '<table border="1" style="width:100%; text-align:left;">';
    echo '<thead>';
    echo '<tr>';
    echo '<th>Caption</th>';
    echo '<th>Who Added</th>';
    echo '<th>Visibility</th>';
    echo '<th>Date Added</th>';
    echo '<th>Actions</th>';
    echo '</tr>';
    echo '</thead>';
    echo '<tbody>';

    // Wyświetlamy każdy wiersz danych w tabeli
    while ($row = $result->fetch_assoc()) {
        $visibility = $row['is_deleted'] == 0 ? 'Tak' : 'Nie';
        $action = $row['is_deleted'] == 0 ? 
                  '<a href="#" onclick="toggleVisibility(' . $row['image_id'] . ', 0)">Usuń</a>' : 
                  '<a href="#" onclick="toggleVisibility(' . $row['image_id'] . ', 1)">Przywróć</a>';

        echo '<tr>';
        echo '<td>' . htmlspecialchars($row['caption']) . '</td>';
        echo '<td>' . htmlspecialchars($row['username']) . '</td>';
        echo '<td>' . $visibility . '</td>';
        echo '<td>' . $row['created_at'] . '</td>';
        echo '<td>' . $action . '</td>';
        echo '</tr>';
    }

    echo '</tbody>';
    echo '</table>';
} else {
    echo 'Brak kwejków w bazie danych.';
}
?>
