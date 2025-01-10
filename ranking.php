<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Ranking - KWEJK.pl</title>
    <link rel="stylesheet" href="./css/ranking.css" />
    <style>
        /* Style for filter buttons to match tab buttons */
        .filter-buttons {
            display: flex;
            gap: 10px;
            margin-bottom: 15px;
        }

        .filter-button {
            background-color: #f0f0f0;
            border: 1px solid #ccc;
            border-radius: 5px;
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s, border-color 0.3s;
        }

        .filter-button:hover {
            background-color: #e0e0e0;
            border-color: #999;
        }

        .filter-button.active {
            background-color: #ddd;
            border-color: #666;
        }
    </style>
</head>
<body>
<header class="main-header">
    <div class="header-content">
        <a href="./index.php" class="logo">
            <img src="./images/kwejk-logo.png" alt="KWEJK.pl" />
        </a>
        <nav class="main-nav">
            <a href="./dodaj.php" class="add-button">+ Add</a>
            <a href="./ranking.php">Top</a>
        </nav>
        <div class="auth-buttons" style="display: <?php echo htmlspecialchars($username) == null ? 'block' : 'none'; ?>;">
            <a href="./login.php">Login</a>
            <a href="./register.php" class="register-button">Register</a>
        </div>
        <?php
        if (isset($_SESSION['logged']) && $_SESSION['logged'] === true) {
            echo '
            <div class="user-profile" style="display: ' . (htmlspecialchars($username) == null ? 'none' : 'block') . ';">
                <a href="./profile.php" class="user-button">' . htmlspecialchars($username) . ($isAdmin == 0 ? '(user)' : '(admin)') . '</a>
            </div>';
            if ($isAdmin == 1) {
                echo '
                <div class="user-profile">
                    <a href="./acp.php" class="user-button">Admin Panel</a>
                </div>';
            }
            echo '<div class="user-profile" style="display:' . (htmlspecialchars($username) == null ? 'none' : 'block') . ';">
                <a href="./logout.php" class="user-button">Log Out</a>
            </div>';
        }
        ?>
    </div>
</header>
<main class="content">
    <div class="ranking-container">
        <h1>Ranking</h1>
        <div class="tabs">
            <button class="tab-button active" data-tab="posts">Posts</button>
            <button class="tab-button" data-tab="users">Users</button>
        </div>
        <div class="tab-content active" id="posts-ranking">
            <h2>Top 10 Posts</h2>
            <div class="filter-buttons">
                <button class="filter-button" data-filter="latest">Most Recent</button>
                <button class="filter-button" data-filter="most_liked">Most Liked</button>
            </div>
            <ol class="ranking-list" id="posts-list">
                <!-- Data will be loaded via AJAX -->
            </ol>
        </div>
        <div class="tab-content" id="users-ranking">
            <h2>Top 10 Users</h2>
            <div class="filter-buttons">
                <button class="filter-button" data-filter="most_active">Most Active</button>
                <button class="filter-button" data-filter="highest_rated">Highest Rated</button>
            </div>
            <ol class="ranking-list" id="users-list">
                <!-- Data will be loaded via AJAX -->
            </ol>
        </div>
    </div>
</main>
<script src="post_ranking.js"></script>
<script src="user_ranking.js"></script>
</body>
</html>
