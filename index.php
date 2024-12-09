<!DOCTYPE html>
<html lang="pl">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>KWEJK.pl</title>
    <link rel="stylesheet" href="styles.css" />
  </head>
  <body>
    <header class="main-header">
      <div class="header-content">
      <a href="./index.php" class="logo">
          <img src="./images/kwejk-logo.png" alt="KWEJK.pl" />
        </a>
        <nav class="main-nav">
          <a href="/dodaj" class="add-button">+ Dodaj</a>
          <a href="/ranking">Top</a>
        </nav>
        <div class="auth-buttons">
          <a href="./Login/login.php">Logowanie</a>
          <a href="./Register/register.php" class="register-button"
            >Rejestracja</a
          >
        </div>
      </div>
    </header>

    <main class="content">
      <div class="content-wrapper">
        <section class="posts">
          <article class="post">
            <div class="post-header">
              <img src="./images/avatar.webp" alt="" class="avatar" />

              <span class="author">xxx</span>
            </div>
            <h2 class="post-title">xxx</h2>
            <div class="post-tags">
              <span class="tag">tag1</span>
              <span class="tag">tag2</span>
              <span class="tag">#hash</span>
            </div>
            <div class="post-content">
              <img
                src="./images/post1.jpg"
                alt="Post image"
                class="post-image"
              />
            </div>
            <div class="post-actions">
              <button class="vote-up">+</button>
              <span class="vote-count">213</span>
              <button class="vote-down">-</button>
              <div class="social-buttons">
                <div class="fb-like"></div>
              </div>
            </div>
          </article>
        </section>

        <aside class="sidebar">
          <div class="top-users">
            <h3>Top 3 użytkowników:</h3>
            <ol class="user-list">
              <li>
                <img src="./images/avatar.webp" alt="" />
                <span class="username">xxx</span>
                <span class="points">1156 pkt.</span>
              </li>
              <li>
                <img src="./images/avatar.webp" alt="" />
                <span class="username">xxx</span>
                <span class="points">986 pkt.</span>
              </li>
            </ol>
          </div>
        </aside>
      </div>
    </main>

  </body>
</html>