<!DOCTYPE html>
<html lang="pl">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Rejestracja - KWEJK.pl</title>
    <link rel="stylesheet" href="styles.css" />
  </head>
  <body>
    <header class="main-header">
      <div class="header-content">
        <a href="/" class="logo">
          <img src="../images/kwejk-logo.png" alt="KWEJK.pl" />
        </a>
      </div>
    </header>

    <?php

    include('config.php');   
    
    if(isset($_POST['rejestruj'])){
        $password = $_POST['password'];
        $confirmPassword = $_POST['confirm-password'];
        $username = $_POST['username'];
    
    if ($password !== $confirmPassword) {
        echo '<p>Hasła nie są takie same!</p>';
    } else {
        if(strlen($password) < 8) {
            echo '<p>Hasło musi mieć co najmniej 8 znaków.</p>';
        } elseif (!preg_match('/[A-Z]/', $password)) {
            echo '<p>Hasło musi zawierać co najmniej jedną dużą literę.</p>';
        } elseif (!preg_match('/[0-9]/', $password)) {
            echo '<p>Hasło musi zawierać co najmniej jedną cyfrę.</p>';
        } elseif (!preg_match('/[\W_]/', $password)) {
            echo '<p>Hasło musi zawierać co najmniej jeden znak specjalny.</p>';
        } else {
            $sql = "SELECT username FROM users WHERE username = '{$username}'";
            $result = $mysqli->query($sql);
    
            if($result->num_rows == 0){
                $pass = codepass($password); 
                $sql = "INSERT INTO users (username, password_hash) VALUES ('{$username}', '".$pass."')";
                if ($mysqli->query($sql)) {
                    echo '<p>Pomyślnie zarejestrowano! Możesz się zalogować.</p>';						
                } else {
                    echo '<p>Błąd dodania</p>';
                    echo $mysqli->error;
                }
            } else {
                echo '<p>Istnieje już użytkownik z taką nazwą użytkownika</p>';
            }
        }
    }

    ?>

    <main class="content">
      <div class="auth-container">
        <h1>Rejestracja</h1>
        <form action="register.php" method="POST" id="register-form" class="auth-form">
        <div class="form-group">
            <label for="register-username">Nazwa użytkownika:</label>
            <input
              type="text"
              id="register-username"
              name="username"
              required
            />
          </div>
          <div class="form-group">
            <label for="register-password">Hasło:</label>
            <input
              type="password"
              id="register-password"
              name="password"
              required
            />
          </div>
          <div class="form-group">
            <label for="register-confirm-password">Potwierdź hasło:</label>
            <input
              type="password"
              id="register-confirm-password"
              name="confirm-password"
              required
            />
          </div>
          <button type="submit" class="submit-button" name="rejestruj">Zarejestruj się</button>
        </form>
        <p class="auth-switch">
          Masz już konto? <a href="../Login/login.html">Zaloguj się</a>
        </p>
      </div>
    </main>
  </body>
</html>
