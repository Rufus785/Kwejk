<!DOCTYPE html>
<html lang="pl">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Dodaj - KWEJK.pl</title>
    <link rel="stylesheet" href="./css/dodaj.css" />
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
            header("Location: login.php");
            exit();
        }
    } 
    
    if(isset($_POST['dodaj'])){
        $image = $_POST['image'];
        $caption = $_POST['caption'];
        $sql = "INSERT INTO images (user_id, caption, image_url) VALUES ('{$user_id}', '".$caption."', '{$image}' )";
            if ($mysqli->query($sql)) {
                echo '<p class="php-message success">Post pomyślnie dodany!</p>';					
              } else {
                  echo '<p class="php-message error">Błąd dodania</p>';
                  echo $mysqli->error;
              }
            header("Location: index.php");
            exit();
    }
    ?>

    <main class="content">
      <div class="auth-container">
        <h1>Dodaj</h1>
        <form action="dodaj.php" method="POST" id="add-form" class="add-form">
        <div class="form-group">
            <label for="image-ulr">Image url:</label>
            <input
              type="text"
              id="image-ulr"
              name="image"
              required
            />
          </div>
          <div class="form-group">
            <label for="image-cap">Caption:</label>
            <input
              type="text"
              id="image-cap"
              name="caption"
            />
          </div>
          <button type="submit" class="submit-button" name="dodaj">Dodaj</button>
        </form>
      </div>
    </main>
  </body>
</html>