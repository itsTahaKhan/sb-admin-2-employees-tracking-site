<?php 

if($action==='fetchNotifications'){    
$stmt = $conn->prepare("SELECT design_name FROM employeedesignations WHERE emp_id = ?");
    $stmt->bind_param('i',$_SESSION['id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $isAdmin = false;
    while($r = $result->fetch_assoc()){
        $designation = trim(strtolower($r['design_name']));
        if($designation === 'admin'){
            $isAdmin=true;
            break;
        }
    }

    if($isAdmin!==true){
        return;
    }
    

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