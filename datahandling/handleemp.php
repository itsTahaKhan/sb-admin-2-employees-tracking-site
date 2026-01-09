<?php 

include 'classes\employee.php';

if ($action === 'fetchEmployeesServer') {

    $start  = $_POST['start'] ?? 0;      
    $length = $_POST['length'] ?? 10;    
    $search = $_POST['search']['value'] ?? '';

    $where = "";
    if ($search !== "") {
        $where = "WHERE e.fname LIKE '%$search%' 
                  OR e.lname LIKE '%$search%' 
                  OR e.email LIKE '%$search%'
                  OR e.emp_id LIKE '%$search%'
                  OR ed.design_name LIKE'%$search%' ";
    }

    $totalRes = $conn->query("SELECT COUNT(*) AS cnt FROM employees");
    $total = $totalRes->fetch_assoc()['cnt'];

    $query = "
        SELECT e.emp_id, e.fname, e.lname, e.email,
               GROUP_CONCAT(ed.design_name) AS designations
        FROM employees e
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
        "action"      => "
            <button class='ml-3 btn btn-sm btn-info updEmp' 
                data-id='{$row['emp_id']}'
                data-fname='{$row['fname']}'
                data-lname='{$row['lname']}'
                data-email='{$row['email']}'
                data-designations='{$row['designations']}'>Update</button>
            <button style='float:right;' class='mr-3 btn btn-sm btn-danger delEmp'
                data-id='{$row['emp_id']}'>Delete</button>
        "
    ];
}

    echo json_encode([
        "draw"            => intval($_POST['draw']),
        "recordsTotal"    => $total,
        "recordsFiltered" => ($where === "" ? $total : count($rows)),
        "data"            => $rows
    ]);
    exit;
} 

// -------------------- ADD Employee --------------------
if ($action === 'addEmployee') {
    $emp_id = intval($_POST['emp_id'] ?? 0);
    $fname = trim(ucwords(trim($_POST['fname'])) ?? '');
    $lname = trim(ucwords(trim($_POST['lname'])) ?? '');
    $email = strtolower(trim($_POST['email'] ?? ''));
    $design = $_POST['designation'] ?? [];
    $pass = trim($_POST['pass'] ?? '');
    $employee = new Employee();
    $employee->setID($emp_id);
    $employee->setFname($fname);
    $employee->setLname($lname);
    $employee->setEmail($email);
    $employee->setDesignation($design);
    $employee->setPass($pass);
    if(!$employee->isValid()){
        validationError($employee->getErrors());
    }
    $employee->addValidation();
    if(!$employee->isValid()){
        validationError($employee->getErrors());
    }
    $employee->addEmployee();
}
// -------------------- UPDATE Employee --------------------
if ($action === 'updateEmployee') {
    $orig = intval($_POST['orig_emp_id'] ?? 0);
    $emp_id = intval($_POST['emp_id'] ?? 0);
    $fname = trim(ucwords($_POST['fname']) ?? '');
    $lname = trim(ucwords($_POST['lname']) ?? '');
    $email = trim(strtolower($_POST['email'] ?? ''));
    $design = $_POST['designation'] ?? [];
    $employee = new Employee();
    $employee->setID($emp_id);
    $employee->setFname($fname);
    $employee->setLname($lname);
    $employee->setEmail($email);
    $employee->setDesignation($design);
    $employee->setOrig($orig);
    if(!$employee->isValid()){
        validationError($employee->getErrors());
    }
    $employee->updateValidation();
    if(!$employee->isValid()){
        validationError($employee->getErrors());
    }
    $employee->updateEmployee();
}

// -------------------- DELETE Employee --------------------
if ($action === 'deleteEmployee') {
    $emp_id = intval($_POST['emp_id'] ?? 0);
    $employee = new Employee();
    $employee->setID($emp_id);
    $employee->deleteEmployee();
}

include 'empfileupload.php';