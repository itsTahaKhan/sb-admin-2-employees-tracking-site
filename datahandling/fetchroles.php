<?php 

if ($action === 'fetchRolesOptions') {
    $res = $conn->query("
        SELECT role_id, role_name 
        FROM roles 
        ORDER BY role_name
    ");

    // required for allowClear
    echo "<option value=''>--None--</option>";

    if ($res) {
        while ($row = $res->fetch_assoc()) {
            $id   = (int)$row['role_id'];
            $name = htmlspecialchars(ucwords($row['role_name']), ENT_QUOTES);
            echo "<option value='{$id}'>{$name}</option>";
        }
    }
    exit;
}
