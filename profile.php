<?php
require_once 'config.php';

if (!$_SESSION['logged']) {
    die("Musisz być zalogowany, aby edytować profil.");
}


$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];

    if (isset($_POST['change_password'])) {
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];

        if ($new_password !== $confirm_password) {
            $message = "Nowe hasła nie pasują do siebie!";
        } else {
            $result = $mysqli->query("SELECT password_hash FROM Users WHERE user_id = $user_id");
            $user = $result->fetch_assoc();

            if (!password_verify($current_password, $user['password_hash'])) {
                $message = "Aktualne hasło jest nieprawidłowe!";
            } else {
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $mysqli->query("UPDATE Users SET password_hash = '$hashed_password' WHERE user_id = $user_id");
                $message = "Hasło zostało zmienione pomyślnie!";
            }
        }
    }
}

    if (isset($_POST['change_nickname'])) {
        $new_nickname = $_POST['new_nickname'];

        $result = $mysqli->query("SELECT user_id FROM Users WHERE username = '$new_nickname'");
        if ($result->num_rows > 0) {
            $message = "Ten nick jest już zajęty!";
        } else {
            $mysqli->query("UPDATE Users SET username = '$new_nickname' WHERE user_id = $user_id");
            $message = "Nick został zmieniony pomyślnie!";
        }
    }

?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil użytkownika - KWEJK.pl</title>
    <link rel="stylesheet" href="./css/profile.css">
</head>
<body>
    <header class="main-header">
        <div class="header-content">
            <a href="./index.php" class="logo">
                <img src="./images/kwejk-logo.png" alt="KWEJK.pl" />
            </a>
            <nav class="main-nav">
                <a href="./dodaj.php" class="add-button">+ Dodaj</a>
                <a href="./ranking.php">Top</a>
            </nav>
            <div class="user-profile">
                <a href="./logout.php" class="user-button">Wyloguj mnie</a>
            </div>
        </div>
    </header>

    <main class="content">
        <div class="profile-container">
            <h1>Profil użytkownika</h1>


            <?php if (!empty($message)): ?>
                <div class="message">
                    <p><?php echo htmlspecialchars($message); ?></p>
                </div>
            <?php endif; ?>


            <div class="profile-section">
                <h2>Zmień hasło</h2>
                <form action="profile.php" method="post">
                    <input type="password" name="current_password" placeholder="Aktualne hasło" required>
                    <input type="password" name="new_password" placeholder="Nowe hasło" required>
                    <input type="password" name="confirm_password" placeholder="Potwierdź nowe hasło" required>
                    <button type="submit" name="change_password">Zmień hasło</button>
                </form>
            </div>


            <div class="profile-section">
                <h2>Zmień nick</h2>
                <form action="profile.php" method="post">
                    <input type="text" name="new_nickname" placeholder="Nowy nick" required>
                    <button type="submit" name="change_nickname">Zmień nick</button>
                </form>
            </div>
        </div>
    </main>
</body>
</html>
