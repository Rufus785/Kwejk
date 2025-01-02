<?php
include("config.php"); // Łączymy się z bazą danych (zakładając, że config.php już wywołuje session_start())

// Sprawdzamy, czy użytkownik jest zalogowany
if (!isset($_SESSION['logged']) || $_SESSION['logged'] !== true) {
    echo 'Musisz być zalogowany, aby wykonać tę operację.';
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
    echo 'Nie masz uprawnień administratora do wykonania tej operacji.';
    exit();
}

// Pobieramy dane z zapytania
$image_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$action = isset($_GET['action']) ? (int)$_GET['action'] : 0; // 0 - usunięcie, 1 - przywrócenie

// Jeżeli nie podano poprawnych danych, zatrzymujemy operację
if ($image_id === 0 || !in_array($action, [0, 1])) {
    echo 'Nieprawidłowe dane!';
    exit();
}

// Ustawiamy wartość `is_deleted` na 0 (przywrócenie) lub 1 (usunięcie)
$new_status = $action === 0 ? 1 : 0;  // 0 -> usunięcie (1 -> przywrócenie)

// Zmieniamy status `is_deleted` dla danego kwejka
$sql = "UPDATE images SET is_deleted = ? WHERE image_id = ?";
$stmt = $mysqli->prepare($sql);
if ($stmt === false) {
    die('Błąd w przygotowaniu zapytania: ' . $mysqli->error);
}

$stmt->bind_param('ii', $new_status, $image_id);
if ($stmt->execute()) {
    // Operacja zakończona sukcesem, zwracamy komunikat sukcesu
    echo 'OK';
} else {
    // Wystąpił błąd przy zmianie statusu
    echo 'Wystąpił błąd przy zmianie statusu kwejka.';
}
?>
