<!DOCTYPE html>
<html lang="pl">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Logowanie - KWEJK.pl</title>
    <link rel="stylesheet" href="./css/login.css" />
  </head>
  <body>
    <header class="main-header">
      <div class="header-content">
      <a href="./index.php" class="logo">
          <img src="./images/kwejk-logo.png" alt="KWEJK.pl" />
        </a>
      </div>
    </header>


    <?php
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

        include("config.php");

        if (isset($_POST['loguj'])) {
            if (!empty($_POST['password']) && !empty($_POST['login'])) {
                $pass = $_POST['password'];
                $sql = "SELECT user_id, password_hash FROM users WHERE username = ? LIMIT 1";
                $stmt = $mysqli->prepare($sql);
                $stmt->bind_param('s', $_POST['login']);
                $stmt->execute();
                $result = $stmt->get_result();
                
                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    if (password_verify($pass, $row['password_hash'])) {
                        $_SESSION['logged'] = true;
                        $_SESSION['user_id'] = $row['user_id'];
                        header("Location: index.php");
                        exit();
                    } else {
                        echo '<p>Błędne hasło!</p>';
                    }
                } else {
                    echo '<p>Błędny login!</p>';
                }
            } else {
                echo '<p>Wszystkie pola muszą być wypełnione!</p>';
            }
        }
    ?>


    <main class="content">
      <div class="auth-container">
        <h1>Logowanie</h1>
        <form action="login.php" method="POST" id="login-form" class="auth-form">
          <div class="form-group">
            <label for="login-email">Nazwa użytkownika:</label>
            <input type="" id="login-email" name="login" required />
          </div>
          <div class="form-group">
            <label for="login-password">Hasło:</label>
            <input
              type="password"
              id="login-password"
              name="password"
              required
            />
          </div>
          <button type="submit" class="submit-button" name="loguj">Zaloguj się</button>
        </form>
        <p class="auth-switch">
          Nie masz konta?
          <a href="./register.php">Zarejestruj się</a>
        </p>
      </div>
    </main>

  </body>
</html>
