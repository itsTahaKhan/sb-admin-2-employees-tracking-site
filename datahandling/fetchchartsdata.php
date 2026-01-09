<?php 


if ($action === 'fetchBarDataMonth') {

    $sql = "
        SELECT 
            SUM(last_activity >= (NOW() - INTERVAL 30 DAY)) AS active_users,
            SUM(last_activity < (NOW() - INTERVAL 30 DAY) OR last_activity IS NULL) AS inactive_users,
            SUM(DATE(last_activity) = CURDATE()) AS daily_active_users
        FROM userdata
    ";

    $res = $conn->query($sql);

    if (!$res) {
        echo json_encode(["error" => $conn->error]);
        exit;
    }

    $data = [
        "active" => [],
        "inactive" => [],
        "daily" => []
    ];

    while ($row = $res->fetch_assoc()) {
        $data['active'] = (int)$row['active_users'];
        $data['inactive'] = (int)$row['inactive_users'];
        $data['daily'] = (int)$row['daily_active_users'];
    }

    echo json_encode($data);
    exit;
}

if ($action === 'fetchBarDataWeek') {

    $sql = "
        SELECT 
            SUM(last_activity >= (NOW() - INTERVAL 6 DAY)) AS active_users,
            SUM(last_activity < (NOW() - INTERVAL 6 DAY) OR last_activity IS NULL) AS inactive_users,
            SUM(DATE(last_activity) = CURDATE()) AS daily_active_users
        FROM userdata
    ";

    $res = $conn->query($sql);

    if (!$res) {
        echo json_encode(["error" => $conn->error]);
        exit;
    }

    $data = [
        "active" => [],
        "inactive" => [],
        "daily" => []
    ];

    while ($row = $res->fetch_assoc()) {
        $data['active'] = (int)$row['active_users'];
        $data['inactive'] = (int)$row['inactive_users'];
        $data['daily'] = (int)$row['daily_active_users'];
    }

    echo json_encode($data);
    exit;
}

if ($action === 'fetchAreaChart') {

    $labels = [];
    $data   = [];

    $range = $_POST['range'] ?? 'areamonth';

    if ($range === 'areaday') {

        $labels = [];
        $data   = [];

        for ($i = 0; $i < 24; $i++) {
            $labels[] = str_pad($i, 2, '0', STR_PAD_LEFT);
            $data[$i] = 0;
        }

        $sql = "
            SELECT 
                HOUR(created_at) AS h, 
                COUNT(*) AS total
            FROM employees
            WHERE DATE(created_at) = CURDATE()
            GROUP BY h
            ORDER BY h
        ";


        $res = $conn->query($sql);
        while ($row = $res->fetch_assoc()) {
            $data[(int)$row['h']] = (int)$row['total'];
        }
    }

    elseif ($range === 'areaweek') {

        for ($i = 6; $i >= 0; $i--) {
            $day = date('Y-m-d', strtotime("-$i days"));
            $labels[] = date('D', strtotime($day));
            $data[$day] = 0;
        }

        $sql = "
            SELECT DATE(created_at) AS d, COUNT(*) AS total
            FROM employees
            WHERE created_at >= CURDATE() - INTERVAL 6 DAY
            GROUP BY d
        ";

        $res = $conn->query($sql);
        while ($row = $res->fetch_assoc()) {
            $data[$row['d']] = (int)$row['total'];
        }



    }
    
    elseif($range==='areamonth'){ 
        for ($i = 3; $i >= 0; $i--) {
        $week = date('oW', strtotime("-$i week"));
        $labels[] = 'Week ' . substr($week, -2);
        $data[$week] = 0;
    }

        $sql = "
            SELECT YEARWEEK(created_at, 3) AS yw, COUNT(*) AS total
            FROM employees
            WHERE created_at >= CURDATE() - INTERVAL 30 DAY
            GROUP BY yw
        ";

        $res = $conn->query($sql);
        while ($row = $res->fetch_assoc()) {
            $data[$row['yw']] = (int)$row['total'];
        }
    }

    echo json_encode([
        'labels' => $labels,
        'data'   => array_values($data)
    ]);
    exit;
    
}
