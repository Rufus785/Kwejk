<!DOCTYPE html>
<html lang="pl">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>KWEJK.pl</title>
    <link rel="stylesheet" href="styles.css" />
    <link rel="stylesheet" href="css/acp.css" />
  </head>

  <?php

      include("config.php");
  
      if (isset($_SESSION['logged']) && $_SESSION['logged'] === true) {
          $user_id = $_SESSION['user_id'];
          $sql = "SELECT username FROM users WHERE user_id = ? LIMIT 1";
          $stmt = $mysqli->prepare($sql);
          $stmt->bind_param('i', $user_id);
          $stmt->execute();
          $result = $stmt->get_result();
          
          if ($result->num_rows > 0) {
              $row = $result->fetch_assoc();
              $username = $row['username'];
          } else {
              unset($_SESSION['logged']);
              unset($_SESSION['user_id']);
              $username = null;
          }

          $sql = "SELECT is_admin FROM users WHERE user_id = ? LIMIT 1";
          $stmt = $mysqli->prepare($sql);
          $stmt->bind_param('i', $user_id);
          $stmt->execute();
          $result = $stmt->get_result();
          
          if ($result->num_rows > 0) {
              $row = $result->fetch_assoc();
              $isAdmin = $row['is_admin'];
          } else {
              unset($_SESSION['logged']);
              unset($_SESSION['user_id']);
              $isAdmin = null;
          }
      }

      $sql = "SELECT k.image_id, u.username, k.caption, k.image_url, k.created_at
        FROM Images k
        JOIN users u ON k.user_id = u.user_id
        WHERE k.is_deleted = 0
        ORDER BY k.created_at DESC";
        $stmt = $mysqli->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();

        $posts = [];
        while ($row = $result->fetch_assoc()) {
            $image_id = $row['image_id'];

            // Pobieranie komentarzy dla danego obrazu
            $comment_sql = "SELECT c.comment_text, c.created_at, u.username 
                            FROM Comments c
                            JOIN users u ON c.user_id = u.user_id
                            WHERE c.image_id = ?
                            ORDER BY c.created_at ASC";
            $comment_stmt = $mysqli->prepare($comment_sql);
            $comment_stmt->bind_param('i', $image_id);
            $comment_stmt->execute();
            $comments_result = $comment_stmt->get_result();
            $comments = [];
            while ($comment_row = $comments_result->fetch_assoc()) {
                $comments[] = $comment_row;
            }
            $row['comments'] = $comments; // Dołącz komentarze do postu
            $comment_stmt->close();

            // Sprawdzenie liczby polubień
            $like_count_sql = "SELECT COUNT(*) AS like_count FROM Likes WHERE image_id = ?";
            $like_count_stmt = $mysqli->prepare($like_count_sql);
            $like_count_stmt->bind_param('i', $image_id);
            $like_count_stmt->execute();
            $like_count_result = $like_count_stmt->get_result()->fetch_assoc();
            $row['like_count'] = $like_count_result['like_count'];
            $like_count_stmt->close();

            // Sprawdzenie, czy użytkownik już polubił post
            $user_liked_sql = "SELECT COUNT(*) AS user_liked FROM Likes WHERE image_id = ? AND user_id = ?";
            $user_liked_stmt = $mysqli->prepare($user_liked_sql);
            $user_liked_stmt->bind_param('ii', $image_id, $user_id);
            $user_liked_stmt->execute();
            $user_liked_result = $user_liked_stmt->get_result()->fetch_assoc();
            $row['user_liked'] = $user_liked_result['user_liked'] > 0;
            $user_liked_stmt->close();

            // Dodanie do tablicy $posts
            $posts[] = $row;
        }

        $stmt->close();

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment_text'], $_POST['image_id'])) {
          $comment_text = $_POST['comment_text'];
          $image_id = $_POST['image_id'];
          $user_id = $_SESSION['user_id'];
      
          // Wstawianie komentarza do bazy danych
          $sql = "INSERT INTO Comments (image_id, user_id, comment_text) VALUES (?, ?, ?)";
          $stmt = $mysqli->prepare($sql);
          $stmt->bind_param('iis', $image_id, $user_id, $comment_text);
          if ($stmt->execute()) {
              header("Location: " . $_SERVER['PHP_SELF']);
              exit;
          } else {
              echo "Błąd podczas dodawania komentarza.";
          }
          $stmt->close();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['like_action'], $_POST['image_id'])) {
          $like_action = $_POST['like_action'];
          $image_id = $_POST['image_id'];
      
          if ($like_action === 'like') {
              // Dodaj polubienie
              $like_sql = "INSERT IGNORE INTO Likes (image_id, user_id) VALUES (?, ?)";
              $like_stmt = $mysqli->prepare($like_sql);
              $like_stmt->bind_param('ii', $image_id, $user_id);
              $like_stmt->execute();
              $like_stmt->close();
          } elseif ($like_action === 'unlike') {
              // Usuń polubienie
              $unlike_sql = "DELETE FROM Likes WHERE image_id = ? AND user_id = ?";
              $unlike_stmt = $mysqli->prepare($unlike_sql);
              $unlike_stmt->bind_param('ii', $image_id, $user_id);
              $unlike_stmt->execute();
              $unlike_stmt->close();
          }
      
          // Odświeżenie strony
          header("Location: " . $_SERVER['PHP_SELF']);
          exit;
      }
      
  ?>

  <body>
    <header class="main-header">
      <div class="header-content">
      <a href="./index.php" class="logo">
          <img src="./images/kwejk-logo.png" alt="KWEJK.pl" />
        </a>
        <nav class="main-nav">
          <a href="./dodaj.php" class="add-button">+ Dodaj</a>
          <a href="/ranking">Top</a>
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
      <div class="content-wrapper">
        <section class="posts">
        <?php foreach ($posts as $post): ?>
            <article class="post">
                <div class="post-header">
                    <img src="./images/avatar.webp" alt="Avatar" class="avatar" />
                    <span class="author"><?php echo htmlspecialchars($post['username']); ?></span>
                </div>
                <h2 class="post-title"><?php echo htmlspecialchars($post['caption']); ?></h2>
                <div class="post-content">
                    <img src="<?php echo htmlspecialchars($post['image_url']); ?>" alt="Post image" class="post-image" />
                </div>
                <div class="post-actions">
                    <form method="post" action="">
                        <input type="hidden" name="image_id" value="<?php echo $post['image_id']; ?>">
                        <?php if ($post['user_liked']): ?>
                            <button type="submit" name="like_action" value="unlike" class="vote-up liked">Unlike</button>
                        <?php else: ?>
                            <button type="submit" name="like_action" value="like" class="vote-up">Like</button>
                        <?php endif; ?>
                    </form>
                    <span class="vote-count"><?php echo $post['like_count'] ?? 0; ?></span>
                </div>
                <div class="comments">
                    <h3>Komentarze:</h3>
                    <?php if (count($post['comments']) > 0): ?>
                        <?php foreach ($post['comments'] as $comment): ?>
                            <div class="comment">
                                <strong><?php echo htmlspecialchars($comment['username']); ?>:</strong>
                                <p><?php echo htmlspecialchars($comment['comment_text']); ?></p>
                                <small><?php echo htmlspecialchars($comment['created_at']); ?></small>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>Brak komentarzy.</p>
                    <?php endif; ?>
                </div>

                <div class="add-comment">
                    <form method="post" action="">
                        <textarea name="comment_text" placeholder="Dodaj komentarz..." required></textarea>
                        <input type="hidden" name="image_id" value="<?php echo $post['image_id']; ?>" />
                        <button type="submit">Dodaj komentarz</button>
                    </form>
                </div>
            </article>
        <?php endforeach; ?>
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
