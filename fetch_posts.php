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

if ($filter === 'most_liked') {
    $query = "
        SELECT 
            i.caption AS title, 
            COUNT(l.like_id) AS points, 
            i.image_url AS image 
        FROM 
            Images i 
        LEFT JOIN 
            Likes l 
        ON 
            i.image_id = l.image_id 
        WHERE 
            i.is_deleted = 0 
        GROUP BY 
            i.image_id 
        ORDER BY 
            points DESC 
        LIMIT 10;
    ";
} else {
    $query = "
        SELECT 
            i.caption AS title, 
            COUNT(l.like_id) AS points, 
            i.image_url AS image, 
            i.created_at 
        FROM 
            Images i 
        LEFT JOIN 
            Likes l 
        ON 
            i.image_id = l.image_id 
        WHERE 
            i.is_deleted = 0 
        GROUP BY 
            i.image_id 
        ORDER BY 
            i.created_at DESC 
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
