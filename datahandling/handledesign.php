<?php 
    // -------------------- FETCH: Designations (returns <tr> rows) --------------------
if ($action === 'fetchDesignations') {
    header('Content-Type: text/html; charset=utf-8');
    $sql = "SELECT design_name FROM designations ORDER BY design_name";
    $res = $conn->query($sql);
    if (!$res) { echo "<tr><td colspan='2'>DB error</td></tr>"; exit; }
    if ($res->num_rows === 0) {
        echo "<tr><td colspan='2'>No designations found</td></tr>";
        exit;
    }
    while ($row = $res->fetch_assoc()) {
        $d = htmlspecialchars($row['design_name'], ENT_QUOTES);
        echo "<tr>";
        echo "<td>{$d}</td>";
        echo "<td class='table-actions'>
                <button class='ml-3 btn btn-sm btn-info updDes' data-name=\"{$d}\">Update</button>
                <button class='ml-3 btn btn-sm btn-danger delDes' data-name=\"{$d}\">Delete</button>
              </td>";
        echo "</tr>";
    }
    exit;
}



// -------------------- FETCH: options for designation selects --------------------
if ($action === 'fetchDesignationOptions') {
    header('Content-Type: text/html; charset=utf-8');
    $sql = "SELECT design_name FROM designations ORDER BY design_name";
    $res = $conn->query($sql);
    echo("<optgroup>  ------Select Designation----");
    if (!$res || $res->num_rows === 0) {
        echo "<option value=''>No designations</option>";
        exit;
    }
    while ($row = $res->fetch_assoc()) {
        $d = htmlspecialchars($row['design_name'], ENT_QUOTES);
        echo "<option value=\"{$d}\">{$d}</option>";
    }
    echo"</optgroup>";
    exit;
}


// -------------------- ADD: Designation --------------------
if ($action === 'addDesignation') {
    $name = trim($_POST['design_name'] ?? '');
    if ($name === '') jerror('Designation name empty.');
    // check if exists
    $stmt2 = $conn->prepare("SELECT COUNT(*) AS count FROM designations WHERE design_name = ?");
    $stmt2->bind_param("s", $name);
    $stmt2->execute();
    $r = $stmt2->get_result()->fetch_assoc();
    if ($r['count'] > 0) {
        jerror('Designation already exists.');
    }
    $stmt = $conn->prepare("INSERT INTO designations (design_name) VALUES (?)");
    $stmt->bind_param('s', $name);
    if ($stmt->execute()) jsuccess('Designation added.');
    else jerror('Add failed: ' . $stmt->error);
}


// -------------------- UPDATE: Designation --------------------
if ($action === 'updateDesignation') {
    $old = trim($_POST['old_name'] ?? '');
    $new = trim($_POST['new_name'] ?? '');
    if ($old === '' || $new === '') jerror('Missing values.');
    $stmt = $conn->prepare("UPDATE designations SET design_name = ? WHERE design_name = ?");
    $stmt->bind_param('ss', $new, $old);
    if ($stmt->execute()) {
        jsuccess("Updated!");
    } 
    else jerror('Update failed: ' . $stmt->error);
}

// -------------------- DELETE: Designation --------------------
if ($action === 'deleteDesignation') {
    $design = trim($_POST['design_name'] ?? '');
    if ($design === '') jerror('No designation specified.');
    // check employees using it
    $stmt = $conn->prepare("SELECT COUNT(*) AS cnt FROM employeedesignations WHERE design_name = ?");
    $stmt->bind_param('s', $design);
    $stmt->execute();
    $r = $stmt->get_result()->fetch_assoc();
    if ($r && $r['cnt'] > 0) {
        jerror('Cannot delete employees are assigned to this designation.');
    }
    // safe to delete
    $stmt2 = $conn->prepare("DELETE FROM designations WHERE design_name = ?");
    $stmt2->bind_param('s', $design);
    if ($stmt2->execute()) jsuccess('Designation deleted.');
    else jerror('Delete failed: ' . $stmt2->error);
}
