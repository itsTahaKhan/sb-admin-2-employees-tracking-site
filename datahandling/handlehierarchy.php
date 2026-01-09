<?php

if ($action === 'fetchHierarchy') {
    $conn = new mysqli('localhost', 'root' ,'', 'family');

    $start  = $_POST['start'] ?? 0;      
    $length = $_POST['length'] ?? 10;    
    $search = $_POST['search']['value'] ?? '';

    $where = "";
    if ($search !== "") {
        $where = "WHERE p.names LIKE '%$search%' 
                  OR parent.names LIKE '%$search%' ";
    }

    $totalRes = $conn->query("SELECT COUNT(*) AS cnt FROM persons");
    $total = $totalRes->fetch_assoc()['cnt'];

    

    $query = "
        SELECT p.names, parent.names AS parent_names
        FROM persons p
        LEFT JOIN persons parent ON parent.id = p.parent_id
        $where
        ORDER BY p.id
        LIMIT $start, $length
    ";

    $dataRes = $conn->query($query);
    $rows = [];

while ($row = $dataRes->fetch_assoc()) {
    $rows[] = [
        "names"       => $row['names'],
        "parent_names"       => $row['parent_names']
    ];
}

    echo json_encode([
        "draw"            => intval($_POST['draw']),
        "recordsTotal"    => $total,
        "recordsFiltered" => ($where === "" ? $total : count($rows)),
        "data"            => $rows
    ]);
    $conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
    exit;
}

if($action==='hfileupload'){
    if(!isset($_FILES['hcsvFile'])){
        jerror("Upload a file first");
        exit;
    }
    $conn = new mysqli('localhost','root','','family');
    
    $headerExpected = array('names', 'parent_names');

    if($_FILES['hcsvFile']['error'] === 0){

        $file = fopen($_FILES['hcsvFile']['tmp_name'], 'r');
        $headers = fgetcsv($file);
        $headers = array_map('strtolower', $headers);
        if(array_diff($headers, $headerExpected)){
            jerror("Unidentified headers format.");
            exit;
        }
        $fileUploaded = false;
        $counter = 0;
        while(($row = fgetcsv($file, 100, ','))!==false){
            if (count(array_filter($row)) === 0) {
                continue;
            }
            if (count($row) !== count($headers)) {
                continue;
            }
            if (trim( $row[1]) === 'NULL' || trim( $row[1]) === 'null') {
                $row = array_combine($headers,$row);
                $name = trim($row['names']);
                $parent_name = null;
            }
            else{
                $row = array_combine($headers,$row);
                $name = trim($row['names']);
                $parent_name = trim($row['parent_names']);
            }
            
            if(empty($parent_name)){
                $res = $conn->prepare("SELECT COUNT(*) AS cnt FROM persons WHERE names = ?");
                $res->bind_param('s', $name);
                $res->execute();
                $r = $res->get_result()->fetch_assoc();
                if($r && $r['cnt'] > 0){
                    continue;
                }
                else{
                    $stmt = $conn->prepare("INSERT INTO persons (names) VALUES(?)");
                    $stmt->bind_param('s',$name);
                    if($stmt->execute()){
                        $counter++;
                    }
                }
            }
            else{
                $parent_exists = false;
                $res = $conn->prepare("SELECT names, id FROM persons WHERE names = ?");
                $res->bind_param('s', $parent_name);
                $res->execute();
                while($r = $res->get_result()->fetch_assoc()){
                    if($parent_name===$r['names']){
                        $parent_id = $r['id'];
                        $parent_exists = true;
                        break;
                    }
                }
                if($parent_exists===true){
                    $res = $conn->prepare("SELECT COUNT(*) AS cnt FROM persons WHERE names = ?");
                    $res->bind_param('s', $name);
                    $res->execute();
                    $r = $res->get_result()->fetch_assoc();
                    if($r['cnt'] === 0){
                        $stmt = $conn->prepare("INSERT INTO persons(names, parent_id) VALUES (?,?)");
                        $stmt->bind_param('si', $name,$parent_id);
                        if($stmt->execute()){
                            $counter++;
                        }
                   }
                    else{
                        $stmt = $conn->prepare("UPDATE persons SET parent_id = ? WHERE names = ?");
                        $stmt->bind_param('is', $parent_id,$name);
                        if($stmt->execute()){
                            $counter++;
                        }
                    }
                }
                else{
                    $stmt = $conn->prepare("INSERT INTO persons (names) VALUES(?)");
                    $stmt->bind_param('s',$parent_name);
                    if($stmt->execute()){
                        $res1 = $conn->prepare("SELECT id FROM persons WHERE names = ?");
                        $res1->bind_param('s', $parent_name);
                        $res1->execute();
                        $r1 = $res1->get_result()->fetch_assoc();
                        $parent_id = $r1['id'];
                        $res2 = $conn->prepare("SELECT COUNT(*) AS cnt FROM persons WHERE names = ?");
                        $res2->bind_param('s', $name);
                        $res2->execute();
                        $r2 = $res2->get_result()->fetch_assoc();
                        if($r2['cnt']===0){
                            $stmt1 = $conn->prepare("INSERT INTO persons(names, parent_id) VALUES(?,?)");
                            $stmt1->bind_param('si', $name,$parent_id);
                            if($stmt1->execute()){
                                $counter++;
                            }
                        }
                        else{
                            $stmt2 = $conn->prepare("UPDATE persons SET parent_id = ? WHERE names = ?");
                            $stmt2->bind_param('is', $parent_id,$name);
                            if($stmt2->execute()){
                                $counter++;
                            }
                        }
                    }
                }
            }
        }
        if($counter>0){
            jsuccess("Table updated successfully");
        }
        else{
            jsuccess("Error updating table");
        }
    }
}

?>