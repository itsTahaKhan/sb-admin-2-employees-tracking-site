<?php 
    if($action==='changePassword'){
    $oldpass = $_POST['old_pass'];
    $new_pass = $_POST['new_pass'];
    $ver_new_pass = $_POST['ver_new_pass'];
    $email = $_SESSION['email'];   
    
    if($oldpass ==='' || $new_pass ==='' || $ver_new_pass===''){
        jerror('Fill all fields');
    }
    $stmt = $conn->prepare("SELECT pass FROM employees WHERE email = ?");
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $realpass = $result['pass'];
    if($new_pass!==$ver_new_pass){
        jerror("New passwords don't match");
    }
    $newhash = password_hash($new_pass, PASSWORD_DEFAULT);
    if(password_verify($oldpass, $realpass)){
        $stmt2 = $conn->prepare("UPDATE employees SET pass = ? WHERE email = ?");
        $stmt2->bind_param('ss', $newhash, $email);
        if($stmt2->execute()){
            jsuccess('Password updated successfully');
        }
        else{
            jerror('Password not updated');
        }

    }
    else{
        jerror("Current password is wrong");
    }
}