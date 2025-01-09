<?php
header('Content-Type: application/json');

$filter = $_GET['filter'] ?? 'latest';

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "kwejk";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die(json_encode(['error' => 'DB connection error']));
}

if ($filter === 'most_active') {
    //by sum of likes and comments by user
    $query = "
    SELECT 
        u.username, 
        u.profile_picture_url AS avatar, 
        (COUNT(c.comment_id) + COUNT(l.like_id)) AS activity_score
    FROM 
        Users u
    LEFT JOIN 
        Comments c ON u.user_id = c.user_id
    LEFT JOIN 
        Likes l ON u.user_id = l.user_id
    GROUP BY 
        u.user_id
    ORDER BY 
        activity_score DESC
    LIMIT 10;
    ";
} else if ($filter === 'highest_rated') {
    //by sum of likes on user posts
    $query = "
    SELECT 
        u.username, 
        u.profile_picture_url AS avatar, 
        SUM(CASE WHEN i.is_deleted = 0 THEN (SELECT COUNT(*) FROM Likes l WHERE l.image_id = i.image_id) ELSE 0 END) AS points
    FROM 
        Users u
    LEFT JOIN 
        Images i ON u.user_id = i.user_id
    GROUP BY 
        u.user_id
    ORDER BY 
        points DESC
    LIMIT 10;
    ";
}

$result = $conn->query($query);
$data = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
}

echo json_encode($data);
$conn->close();
?>
