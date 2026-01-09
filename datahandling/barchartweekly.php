<?php 


if($action==='fetchWeeklyActive'){
    $start  = $_POST['start'] ?? 0;      
    $length = $_POST['length'] ?? 10;    
    $search = $_POST['search']['value'] ?? '';

    $where = "";
    if ($search !== "") {
        $where = "WHERE (e.fname LIKE '%$search%' 
                  OR e.lname LIKE '%$search%' 
                  OR e.email LIKE '%$search%' 
                  OR e.emp_id LIKE '%$search%' 
                  OR ed.design_name LIKE'%$search%')
                  AND
                  (ud.last_activity >= CURDATE() - INTERVAL 6 DAY)
                ";
    }
    else{
        $where = "WHERE (ud.last_activity >= CURDATE() - INTERVAL 6 DAY)
                ";
    }

    $totalRes = $conn->query("
                                SELECT COUNT(DISTINCT e.emp_id) AS cnt
                                FROM employees e
                                LEFT JOIN userdata ud ON e.emp_id = ud.emp_id
                                WHERE ud.last_activity >= CURDATE() - INTERVAL 6 DAY
                            ");

    $total = $totalRes->fetch_assoc()['cnt'];

    $filteredres = $conn->query("
                                SELECT COUNT(DISTINCT e.emp_id) AS cnt
                                FROM employees e 
                                LEFT JOIN userdata ud ON e.emp_id = ud.emp_id
                                LEFT JOIN employeedesignations ed ON e.emp_id = ed.emp_id
                                $where
                            ");
    $filtered = $filteredres->fetch_assoc()['cnt'];

    $query = "
        SELECT e.emp_id, e.fname, e.lname, e.email,
               GROUP_CONCAT(ed.design_name) AS designations
        FROM employees e
        LEFT JOIN userdata ud ON e.emp_id = ud.emp_id
        LEFT JOIN employeedesignations ed ON e.emp_id = ed.emp_id
        $where
        GROUP BY e.emp_id
        ORDER BY e.emp_id
        LIMIT $start, $length
    ";

    $dataRes = $conn->query($query);

    $rows = [];
while ($row = $dataRes->fetch_assoc()) {
    $rows[] = [
        "emp_id"      => (int)$row['emp_id'],
        "fname"       => $row['fname'],
        "lname"       => $row['lname'],
        "email"       => $row['email'],
        "designations"=> $row['designations'],
    ];
}

    echo json_encode([
        "draw"            => intval($_POST['draw']),
        "recordsTotal"    => $total,
        "recordsFiltered" => $filtered,
        "data"            => $rows
    ]);
    exit;
}

if($action==='fetchWeeklyInactive'){
    $start  = $_POST['start'] ?? 0;      
    $length = $_POST['length'] ?? 10;    
    $search = $_POST['search']['value'] ?? '';

    $where = "";    
    if ($search !== "") {
        $where = "WHERE (e.fname LIKE '%$search%' 
                  OR e.lname LIKE '%$search%' 
                  OR e.emp_id LIKE '%$search%' 
                  OR e.email LIKE '%$search%' 
                  OR ed.design_name LIKE'%$search%')
                  AND  
                  (ud.last_activity < CURDATE() - INTERVAL 6 DAY 
                  OR ud.last_activity IS NULL)
                ";
    }
    else{
        $where = "WHERE (ud.last_activity < CURDATE() - INTERVAL 6 DAY 
                    OR ud.last_activity IS NULL)
                ";
    }

    $totalRes = $conn->query("
                                SELECT COUNT(DISTINCT e.emp_id) AS cnt
                                FROM employees e
                                LEFT JOIN userdata ud ON e.emp_id = ud.emp_id
                                WHERE ud.last_activity < CURDATE() - INTERVAL 6 DAY
                                OR ud.last_activity IS NULL
    ");

    $total = $totalRes->fetch_assoc()['cnt'];

    $filteredres = $conn->query("
                                SELECT COUNT(DISTINCT e.emp_id) AS cnt
                                FROM employees e
                                LEFT JOIN userdata ud ON e.emp_id = ud.emp_id
                                LEFT JOIN employeedesignations ed ON e.emp_id = ed.emp_id
                                $where
                            ");
    $filtered = $filteredres->fetch_assoc()['cnt'];

    $query = "
        SELECT e.emp_id, e.fname, e.lname, e.email,
               GROUP_CONCAT(ed.design_name) AS designations
        FROM employees e
        LEFT JOIN userdata ud ON e.emp_id = ud.emp_id
        LEFT JOIN employeedesignations ed ON e.emp_id = ed.emp_id
        $where
        GROUP BY e.emp_id
        ORDER BY e.emp_id
        LIMIT $start, $length
    ";

    $dataRes = $conn->query($query);

    $rows = [];
while ($row = $dataRes->fetch_assoc()) {
    $rows[] = [
        "emp_id"      => (int)$row['emp_id'],
        "fname"       => $row['fname'],
        "lname"       => $row['lname'],
        "email"       => $row['email'],
        "designations"=> $row['designations'],
    ];
}

    echo json_encode([
        "draw"            => intval($_POST['draw']),
        "recordsTotal"    => $total,
        "recordsFiltered" => $filtered,
        "data"            => $rows
    ]);
    exit;
}