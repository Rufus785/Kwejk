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

    <main class="content">
      <div class="auth-container">
        <h1>Logowanie</h1>
        <form id="login-form" class="auth-form">
          <div class="form-group">
            <label for="login-email">Nazwa użytkownika:</label>
            <input type="" id="login-email" name="email" required />
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
          <button type="submit" class="submit-button">Zaloguj się</button>
        </form>
        <p class="auth-switch">
          Nie masz konta?
          <a href="./register.php">Zarejestruj się</a>
        </p>
      </div>
    </main>

  </body>
</html>
