<?php 
if($action==='markRead'){
    $notif_id = (int)$_POST['notif_id'];
    $isread = 1;
    $stmt = $conn->prepare("
        UPDATE notifications
        SET is_read = ?
        WHERE id = ?
    ");
    $stmt->bind_param('ii',$isread,$notif_id);
    $stmt->execute();
    exit;
}

if($action==='allRead'){
    $query = '
        UPDATE notifications
        SET is_read = 1
    ';
    $conn->query($query);
    echo json_encode([
        'status' => 'success'
    ]);
    exit;
}