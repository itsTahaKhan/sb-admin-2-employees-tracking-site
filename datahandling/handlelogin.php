<?php
if($action === 'login'){
    $login_email = $_POST['login_email'];
    $login_pass = $_POST['login_pass'];
    if($login_email === '' || $login_pass === '') jerror('Enter credentials');
    $stmt = $conn->prepare("
        SELECT emp_id,fname,email,pass,role 
        FROM employees 
        WHERE email = ?
    ");
    $stmt->bind_param('s', $login_email);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_assoc();
    if(!$res){
        jerror("Email doesn't exist");
    }
    $stmt = $conn->prepare("SELECT design_name FROM employeedesignations WHERE emp_id = ?");
    $stmt->bind_param('i',$res['emp_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $isAdmin = false;

    $stored_pass = $res['pass'];
        
    if(password_verify($login_pass, $stored_pass)){
        $_SESSION['id'] = $res['emp_id'];
        $_SESSION['name'] = $res['fname'];
        $_SESSION['email'] = $res['email'];
        $_SESSION['role'] = $res['role'];
        $_SESSION['logged_in'] = true;
        $stmt = $conn->prepare("
            UPDATE userdata
            SET last_activity = NOW(), status = 'active'
            WHERE emp_id = ?
        ");
        $stmt->bind_param("i", $_SESSION['id']);
        $stmt->execute();
        if(empty($_SESSION['csrf_token'])){
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        $stmt = $conn->prepare("
            SELECT p.perm_name AS permissions
            FROM perms p
            LEFT JOIN role_perms rp ON p.perm_id = rp.perm_id
            WHERE rp.role_id = ?
        ");
        $stmt->bind_param('i', $res['role']);
        if(!$stmt->execute()){
            http_response_code(403);
        }
        $res = $stmt->get_result();

        $permissions = [];
        while($r = $res->fetch_assoc()){
            $permissions[] = $r['permissions'];
        }
        $_SESSION['permissions'] = $permissions;
        
        jsuccess("Logging in!");
    }
    else{
        jerror('Incorrect Password');
    }
}

if($action === 'logout'){
    $stmt = $conn->prepare("
        UPDATE userdata
        SET last_activity = NOW(), status = 'inactive'
        WHERE emp_id = ?
    ");
    $stmt->bind_param("i", $_SESSION['id']);
    $stmt->execute();
    session_unset();
    session_destroy();
    jsuccess('Logging out');

}