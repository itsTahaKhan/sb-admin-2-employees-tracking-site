<?php 

if($action==='fetchNotifications'){
    if(!in_array('fetch.notifications', $_SESSION['permissions'], true)){
        jerror("You do not have permission");
        exit;
    }
    
$conn->query("
    DELETE FROM notifications 
    WHERE (created_at < (CURDATE() - INTERVAL 15 DAY))
");

$notifications = [];

$query = $conn->query("
    SELECT id, message, is_read, DATE_FORMAT(created_at, '%d-%m-%Y %H:%i:%s') AS created_at 
    FROM notifications
    ORDER BY created_at DESC
");

while($row = $query->fetch_assoc()){
    $notifications[] = $row;
}

$q = $conn->query("
    SELECT COUNT(*) AS cnt
    FROM notifications
    WHERE is_read = 0
");

$unread = $q->fetch_assoc()['cnt'];

echo json_encode([
    'status' => 'success',
    'data' => $notifications,
    'unread' => $unread
]);
exit;
}