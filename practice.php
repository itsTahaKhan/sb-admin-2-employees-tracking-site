
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
            WHERE created_at >= NOW() - INTERVAL 1 DAY
            GROUP BY h
        ";

        $res = $conn->query($sql);
        while ($row = $res->fetch_assoc()) {
            $data[(int)$row['h']] = (int)$row['total'];
        }
    }
