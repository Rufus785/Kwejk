<!DOCTYPE html>
<html lang="pl">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Ranking - KWEJK.pl</title>
    <link rel="stylesheet" href="./css/ranking.css" />
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
        <div class="auth-buttons" style="display: <?php echo htmlspecialchars($username)==null ? 'block' : 'none'; ?>;">
          <a href="./login.php">Logowanie</a>
          <a href="./register.php" class="register-button">Rejestracja</a>
        </div>
        <?php
        if (isset($_SESSION['logged']) && $_SESSION['logged'] === true){
          echo'
            <div class="user-profile" style="display: '; echo htmlspecialchars($username)==null ? 'none' : 'block';
            echo '">
                <a href="./profile.php" class="user-button">'; echo htmlspecialchars($username); echo $isAdmin==0 ? '(user)' : '(admin)' ;
                echo '</a>
            </div>';
            if($isAdmin == 1){
              echo '
              <div class="user-profile">
                <a href="./acp.php" class="user-button">Admin Panel</a>
                </div>
              ';
            }
            echo'<div class="user-profile" style="display:'; echo htmlspecialchars($username)==null ? 'none' : 'block'; echo'">
                <a href="./logout.php" class="user-button">Wyloguj mnie</a>
            </div>';
        }
        ?>
      </div>
    </header> 
    <main class="content">
      <div class="ranking-container">
        <h1>Ranking</h1>
        <div class="tabs">
          <button class="tab-button active" data-tab="posts">Posty</button>
          <button class="tab-button" data-tab="users">Użytkownicy</button>
        </div>
        <div class="tab-content active" id="posts-ranking">
          <h2>Top 10 Postów</h2>
          <ol class="ranking-list">
            <li>
            <div class="ranking-info">
                <span class="title">Tytuł posta 1</span>
                <span class="points">1000 punktów</span>
              </div>
              <img src="./images/post1.jpg" alt="Post 1" />

            </li>
            <li>
            <div class="ranking-info">
                <span class="title">Tytuł posta 2</span>
                <span class="points">950 punktów</span>
              </div>
              <img src="./images/post2.jpg" alt="Post 2" />

            </li>
          </ol>
        </div>
        <div class="tab-content" id="users-ranking">
          <h2>Top 10 Użytkowników</h2>
          <ol class="ranking-list">
            <li>
              <img
                src="./images/avatar.webp"
                alt="User 1"
                class="user-avatar"
              />
              <div class="ranking-info">
                <span class="username">Użytkownik1</span>
                <span class="points">5000 punktów</span>
              </div>
            </li>
            <li>
              <img
                src="./images/avatar.webp"
                alt="User 2"
                class="user-avatar"
              />
              <div class="ranking-info">
                <span class="username">Użytkownik2</span>
                <span class="points">4800 punktów</span>
              </div>
            </li>
          </ol>
        </div>
      </div>
    </main>
 
    <script src="ranking.js"></script>
  </body>
</html>