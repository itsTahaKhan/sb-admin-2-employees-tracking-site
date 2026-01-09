<?php 
//--------- SetUsername ----------
if($action==='getUserName'){
    if(isset($_SESSION['name'])){
        echo json_encode([
            'status'=>'success', 
            'username'=>$_SESSION['name']
        ]);
    }
    exit;
}

//--------------------Get total users---------------------
if($action==='getTotalUsers'){
    $sql = "SELECT COUNT(*) AS total FROM employees";
    $res = $conn->query($sql);
    $r = $res->fetch_assoc();
    echo json_encode(['totalUsers'=>$r['total']]);
    exit;
}