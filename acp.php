<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin Panel</title>
    <link rel="stylesheet" href="./css/acp.css" />
</head>
<script>
  // Funkcja do zastosowania filtra
function applyFilter() {
    var filterValue = document.getElementById('filter').value;

    // Tworzymy zapytanie AJAX
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            document.getElementById("tabela-raporty").innerHTML = this.responseText;
        }
    };
    
    // Wyślij zapytanie do show_reports_table.php z odpowiednim parametrem
    xmlhttp.open("GET", "show_reports_table.php?filter=" + filterValue, true);
    xmlhttp.send();
}

  document.getElementById('filter').addEventListener('change', function() {
    // Wymuś wysłanie formularza, aby zaktualizować wyniki
    document.getElementById('filterForm').submit();
});
    // Funkcja do ładowania tabeli kwejków (już masz)
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

    // Funkcja do ładowania tabeli raportów
    function showReports() {
        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                document.getElementById("tabela-raporty").innerHTML = this.responseText;
            }
        };
        xmlhttp.open("GET", "show_reports_table.php?q=", true);
        xmlhttp.send();
    }

    // Funkcja do rozwijania szczegółów raportu
    function toggleReportDetails(report_id) {
        var form = document.getElementById('report-details-' + report_id);
        form.style.display = form.style.display === 'none' ? 'block' : 'none';
    }

    function toggleVisibility(image_id, action) {
    var xmlhttp = new XMLHttpRequest();

    xmlhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            // Jeśli operacja zakończyła się sukcesem
            if (this.responseText === 'OK') {
                // Odświeżamy tabelę kwejków
                showKwejks();
            } else {
                alert('Wystąpił błąd: ' + this.responseText);
            }
        }
    };

    // Przygotowujemy zapytanie do pliku PHP odpowiedzialnego za usuwanie kwejka
    xmlhttp.open("GET", "toggle_kwejk.php?id=" + image_id + "&action=" + action, true);
    xmlhttp.send();
} // Funkcja do rozwijania szczegółów raportu
function toggleReportDetails(reportId) {
    var detailsRow = document.getElementById('report-details-' + reportId);
    if (detailsRow.style.display === 'none') {
        detailsRow.style.display = 'table-row';
    } else {
        detailsRow.style.display = 'none';
    }
}

// Funkcja do rozwiązywania raportu (False Positive / True Positive)
function resolveReport(reportId, status) {
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "resolve_report.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onload = function() {
        if (xhr.status === 200 && xhr.responseText === 'OK') {
            // Zaktualizowanie tabeli raportów
            showReports();
            showKwejks();
        } else {
            alert('Wystąpił błąd: ' + xhr.responseText);
        }
    };
    xhr.send("report_id=" + reportId + "&status=" + status);
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
    } else {
        die('dowidzenia!');
    }
?>

<body onload="showKwejks(); showReports();">
    <header class="main-header">
        <div class="header-content">
            <a href="./index.php" class="logo">
                <img src="./images/kwejk-logo.png" alt="KWEJK.pl" />
            </a>
            <nav class="main-nav">
                <a href="./dodaj.php" class="add-button">+ Dodaj</a>
                <a href="./ranking.php">Top</a>
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
                    </div>';
                }
                echo'<div class="user-profile" style="display:'; echo htmlspecialchars($username)==null ? 'none' : 'block'; echo'">
                    <a href="./logout.php" class="user-button">Wyloguj mnie</a>
                </div>';
            }
            ?>
        </div>
    </header>

    <main class="content">
        <h1>To jest panel administracyjny</h1>

        <h2>Raporty</h2>
        <form id="filterForm">
    <label for="filter">Filtruj raporty:</label>
    <select name="filter" id="filter" onchange="applyFilter()">
        <option value="all" <?php if (!isset($_GET['filter']) || $_GET['filter'] == 'all') echo 'selected'; ?>>Wszystkie</option>
        <option value="unresolved" <?php if (isset($_GET['filter']) && $_GET['filter'] == 'unresolved') echo 'selected'; ?>>Nierozwiązane</option>
        <option value="false_positive" <?php if (isset($_GET['filter']) && $_GET['filter'] == 'false_positive') echo 'selected'; ?>>False Positive</option>
        <option value="true_positive" <?php if (isset($_GET['filter']) && $_GET['filter'] == 'true_positive') echo 'selected'; ?>>True Positive</option>
    </select>
</form>

        <div id="tabela-raporty"></div> <!-- Tabela raportów -->

        <h2>Kwejki</h2>
        <div id="tabela-kwejki"></div> <!-- Tabela kwejków -->
    </main>
</body>
</html>
