<!DOCTYPE html>
<html lang="pl">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin Panel</title>
    <link rel="stylesheet" href="styles.css" />
  </head>

  <script>
    function showKwejks() {
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
        document.getElementById("tabela-kwejki").innerHTML = this.responseText;
    }
    };
    xmlhttp.open("GET","show_kwejks_table.php?q=",true);
    xmlhttp.send();
    }
    function toggleVisibility(image_id, action) {
        var xmlhttp = new XMLHttpRequest();
        
        // Ustawiamy, co się stanie, gdy otrzymamy odpowiedź z serwera
        xmlhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                // Jeśli operacja zakończyła się sukcesem, odświeżamy tabelę
                if (this.responseText === 'OK') {
                    // Wywołanie funkcji do odświeżenia tabeli
                    showKwejks();
                } else {
                    alert('Wystąpił błąd: ' + this.responseText);
                }
            }
        };
        
        // Przygotowujemy zapytanie do pliku PHP
        xmlhttp.open("GET", "toggle_kwejk.php?id=" + image_id + "&action=" + action, true);
        xmlhttp.send();
    }

    // Funkcja do odświeżenia tabeli
    function showKwejks() {
        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                document.getElementById("tabela-kwejki").innerHTML = this.responseText;
            }
        };
        xmlhttp.open("GET", "show_kwejks_table.php?q=", true);
        xmlhttp.send();
    }

</script>



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

          if($isAdmin == 0){
            die('dowidzenia');
          }
      }else{
        die('dowidzenia!');
      }
  ?>

  <body onload="showKwejks()">
    <header class="main-header">
      <div class="header-content">
      <a href="./index.php" class="logo">
          <img src="./images/kwejk-logo.png" alt="KWEJK.pl" />
        </a>
        <nav class="main-nav">
          <a href="/dodaj" class="add-button">+ Dodaj</a>
          <a href="/ranking">Top</a>
        </nav>
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
        <h1>To jest panel administracyjny</h1>
        <div id="tabela-kwejki"></div>
      </div>
    </main>

  </body>
</html>