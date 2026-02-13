<?php
if ($action === 'fetchLogs') {

    if (!in_array('logs.fetch', $_SESSION['permissions'], true)) {
        http_response_code(403);
        echo json_encode([]);
        exit;
    }

    $query = "
        SELECT 
            id,
            DATE_FORMAT(created_at, '%d-%m-%Y %H:%i:%s') AS created_at,
            action,
            actor,
            columns_updated,
            values_before,
            values_after
        FROM logs
        ORDER BY created_at DESC
        LIMIT 50
    ";

    $result = $conn->query($query);

    $logs = [];
    while ($row = $result->fetch_assoc()) {
        $logs[] = $row;
    }

    echo json_encode($logs);
    exit;
}
