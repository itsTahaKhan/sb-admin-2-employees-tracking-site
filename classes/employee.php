<?php 
class Employee{
    public $emp_id = null;
    public $fname = '';
    public $lname = '';
    public $email = '';
    public $designation = [];
    public $pass = '';
    public $role = '';
    public $orig = null;
    public $errors = [];
    public $conn;
    public function __construct(){
        $this->conn = new mysqli("localhost", "root", "", "company");
    }
    public function setFname($fname){
        if(strlen($fname) < 2){
            $this->errors['fname'] = ['Name can not be less than 2 characters long.'];
        }
        $this->fname = ucwords($fname);
    }
    public function getFname(): string{
        return $this->fname;
    }
    public function setLname($lname){
        if(strlen($lname) < 2){
            $this->errors['lname'] = ['Last name can not be less than 2 characters long.'];
        }
        $this->lname = ucwords($lname);
    }
    public function getLname(): string{
        return $this->lname;
    }
    public function setID($emp_id){
        if(!filter_var($emp_id,FILTER_VALIDATE_INT) || $emp_id <= 0 || empty($emp_id)){
            $this->errors['emp_id'] = ['Employee code can only be a positive integer'];
        }
        $this->emp_id = $emp_id;
    }
    public function getID(): string{
        return $this->emp_id;
    }

    public function setOrig($orig){
        if(!filter_var($orig,FILTER_VALIDATE_INT) || $orig <= 0 || empty($orig)){
            $this->errors['emp_id'] = ['Employee code can only be a positive integer'];
        }
        $this->orig = $orig;
    }
    public function getOrig(): string{
        return $this->orig;
    }

    public function setRole($role){
        if(!$role){
            $this->errors['role'] = ['Assign a role'];
        }
        $this->role = $role;
    }

    public function getRole(): string{
        return $this->role;
    }

    public function setEmail($email){
        if(!filter_var($email,FILTER_VALIDATE_EMAIL )){
            $this->errors['email'] = ['Not valid email format'];
        }
        $this->email = $email;
    }
    
    public function getEmail(): string{
        return $this->email;
    }
    public function setDesignation($designation){
        if(empty($designation)){
            $this->errors['designation'] = ['Employee must have at least 1 designation'];
        }
        $this->designation = $designation;
    }
    public function getDesignation(): array{
        return $this->designation;
    }
    public function setPass($pass){
        if(strlen($pass) < 4){
            $this->errors['pass'] = ['Password can not be less than 4 characters'];
        }
        $this->pass = $pass;
    }
    public function addValidation(){
        $stmt2 = $this->conn->prepare("SELECT COUNT(*) AS cnt FROM employees WHERE emp_id=?");
        $stmt2->bind_param('i',$this->emp_id);
        $stmt2->execute();
        $r = $stmt2->get_result()->fetch_assoc();
        if($r && $r['cnt'] > 0) {
            $this->errors['emp_id'] = ['Employee exists with same code, enter a valid code.'];
        }
        $stmt3 = $this->conn->prepare('SELECT COUNT(*) AS count FROM employees WHERE email=?');
        $stmt3->bind_param('s',$this->email);
        $stmt3->execute();
        $r = $stmt3->get_result()->fetch_assoc();
        if($r['count'] > 0) {
            $this->errors['email'] = ['Email already exists.'];
        }
    }
    public function updateValidation(){
        $stmt3 = $this->conn->prepare('SELECT COUNT(*) AS count FROM employees WHERE email=? AND emp_id!=?');
        $stmt3->bind_param('si',$this->email,$this->orig);
        $stmt3->execute();
        $r = $stmt3->get_result()->fetch_assoc();
        if($r['count'] > 0) {
            $this->errors['email'] = ['Email already exists.'];
        }
        $stmt2 = $this->conn->prepare("SELECT COUNT(*) AS cnt FROM employees WHERE emp_id=? AND emp_id!=?");
        $stmt2->bind_param('ii',$this->emp_id, $this->orig);
        $stmt2->execute();
        $r = $stmt2->get_result()->fetch_assoc();
        if($r && $r['cnt'] > 0) {
            $this->errors['emp_id'] = ['Employee exists with same code, enter a valid code.'];
        }
    }
    public function getPass(): string{
        return $this->pass;
    }
    public function isValid():bool{
        return empty($this->errors);
    }
    public function getErrors(){
        return $this->errors;
    }
    public function addEmployee(){
        try{
            $this->conn->begin_transaction();
            $hashpass = password_hash($this->pass, PASSWORD_DEFAULT);
            $date = date('Y-m-d H:i:s');
            // Validation Done now adding
            $stmt = $this->conn->prepare("
            INSERT INTO
              employees (emp_id, fname, lname, email, pass)
              VALUES (?,?,?,?,?)           
            ");
            $stmt->bind_param('issss', $this->emp_id, $this->fname, $this->lname, $this->email, $hashpass);
            if(!$stmt->execute()){
                throw new Exception("Adding employee failed");
            }
            $stmt1 = $this->conn->prepare("INSERT INTO employeedesignations (emp_id, design_name) VALUES (?, ?)");
            foreach($this->designation as $des){
                $stmt1->bind_param('is', $this->emp_id,$des);
                if(!$stmt1->execute()){
                    throw new Exception("Adding designation failed");
                }
            }
            $stmt4 = $this->conn->prepare("
                INSERT INTO emp_roles(emp_id, role_id)
                VALUES(?,?)
            ");
            $stmt4->bind_param('ii', $this->emp_id, $this->role);
            if(!$stmt4->execute()){
                throw new Exception("Role not added");
            }
            
            $stmt2 = $this->conn->prepare("
                INSERT INTO userdata(emp_id,status)
                VALUES (?, ?) 
            ");
            $status = 'inactive';
            $stmt2->bind_param('is', $this->emp_id,$status);
            if(!$stmt2->execute()){
                throw new Exception("Could not update status");
            }

            $msg = "New employee created: $this->fname $this->lname";
            $stmt3 = $this->conn->prepare("
                INSERT INTO notifications (message)
                VALUES(?)
            ");
            $stmt3->bind_param('s',$msg);
            if(!$stmt3->execute()){
                throw new Exception("Notification not sent");
            }

            $log = "{$_SESSION['name']} {$_SESSION['lname']} added employee $this->fname $this->lname";

            $stmt5 = $this->conn->prepare("
                INSERT INTO logs (log)
                VALUES(?)
            ");
            $stmt5->bind_param('s', $log);
            if(!$stmt5->execute()){
                throw new Exception("No log maintained");
            }

            $this->conn->commit();
            jsuccess("Employee added");
        }
        catch(Exception $e){
            $this->conn->rollback();
            jerror("Employee not added. ");
        }
    }

    public function deleteEmployee(){
    try{
        $this->conn->begin_transaction();

        $query = $this->conn->query("
            SELECT fname,lname
            FROM employees
            WHERE emp_id = $this->emp_id
        ");
        $res = $query->fetch_assoc();
        
        $stmt = $this->conn->prepare("DELETE FROM employees WHERE emp_id = ?");
        $stmt->bind_param('i', $this->emp_id);
        if(!$stmt->execute()){
            throw new Exception("Employee not deleted");
        }
        
        $fname = $res['fname'];
        $lname = $res['lname'];

        $msg = "Employee deleted: $fname $lname";
        $stmt1 = $this->conn->prepare("
            INSERT INTO notifications (message)
            VALUES(?)
        ");
        $stmt1->bind_param('s',$msg);
        if(!$stmt1->execute()){
            throw new Exception("Notification not sent");
        }

        $log = "{$_SESSION['name']} {$_SESSION['lname']} deleted employee $fname $lname";

        $stmt5 = $this->conn->prepare("
            INSERT INTO logs (log)
            VALUES(?)
        ");
        $stmt5->bind_param('s', $log);
        if(!$stmt5->execute()){
            throw new Exception("No log maintained");
        }

        
        $this->conn->commit();
        jsuccess("Employee deleted successfully");
    }
    catch (Exception $e){
        $this->conn->rollback();
        jerror("Employee not deleted");
    }

    }

    public function updateEmployee(){
        try{
            $this->conn->begin_transaction();

            $stmt6 = $this->conn->prepare("
                SELECT e.emp_id, e.fname, e.lname, e.email,
                GROUP_CONCAT(ed.design_name) AS designations, MAX(er.role_id) AS role
                FROM employees e
                LEFT JOIN employeedesignations ed ON e.emp_id = ed.emp_id
                LEFT JOIN emp_roles er ON e.emp_id = er.emp_id
                WHERE e.emp_id = ?
                GROUP BY e.emp_id
            ");
            $stmt6->bind_param('i', $this->orig);
            if(!$stmt6->execute()){
                throw new Exception("Select not working");
            }
            $result = $stmt6->get_result();
            while($res = $result->fetch_assoc()){
                $id = $res['emp_id'];
                $fname = $res['fname'];
                $lname = $res['lname'];
                $email = $res['email'];
                $designation = $res['designations'];
                $role = intval($res['role']);
            }
            $newValues = [];
            $valuesBefore = [];
            $designation = array_map('trim', explode(',', $designation));
            $updatedColumns = [];
            if($id!==$this->emp_id){
                $updatedColumns[] = 'Employee id';
                $valuesBefore[] = $id;
                $newValues[] = $this->emp_id;
            }
            if($fname!==$this->fname){
                $updatedColumns[] = 'First name';
                $valuesBefore[] = $fname;
                $newValues[] = $this->fname;
            }
            if($lname!==$this->lname){
                $updatedColumns[] = 'Last name';
                $valuesBefore[] = $lname;
                $newValues[] = $this->lname;
            }
            if($email!==$this->email){
                $updatedColumns[] = 'Email';
                $valuesBefore[] = $email;
                $newValues[] = $this->email;
            }
            if(array_diff($designation, $this->designation)){
                $updatedColumns[] = 'Designation';
                $valuesBefore[] = implode(',', $designation);
                $newValues[] = implode(',', $this->designation);
            }
            if($role!=$this->role){
                $updatedColumns[] = 'Role';
                $valuesBefore[] = $role;
                $newValues[] = $this->role;
            }

            $stmt1 = $this->conn->prepare("DELETE FROM employeedesignations WHERE emp_id = ?");
            $stmt1->bind_param('i', $this->orig);
            if(!$stmt1->execute()){
                throw new Exception("Designations not deleted");
            }

            $stmt4 = $this->conn->prepare("
                DELETE FROM emp_roles WHERE emp_id = ?
            ");
            $stmt4->bind_param('i', $this->emp_id);
            if(!$stmt4->execute()){
                throw new Exception("Role not deleted");
            }

            $stmt = $this->conn->prepare("
                UPDATE employees SET emp_id = ?, fname = ?, lname = ?, email = ? WHERE emp_id = ?
            ");
            $stmt->bind_param('isssi', $this->emp_id, $this->fname, $this->lname, $this->email, $this->orig);    
            if(!$stmt->execute()){
                throw new Exception("Employee table not updated");
            }

            $stmt5 = $this->conn->prepare("
                INSERT INTO emp_roles(emp_id, role_id)
                VALUES(?,?)
            ");
            $stmt5->bind_param('ii', $this->emp_id, $this->role);
            if(!$stmt5->execute()){
                throw new Exception("New role not inserted");
            }

            $stmt2 = $this->conn->prepare("INSERT INTO employeedesignations (emp_id, design_name) VALUES (?, ?)");
            foreach($this->designation as $des){
                $stmt2->bind_param('is', $this->emp_id, $des);
                if(!$stmt2->execute()){
                    throw new Exception("New designations not added");
                }
            }

            $msg = "Employee updated: $this->fname $this->lname";
            $stmt3 = $this->conn->prepare("
                INSERT INTO notifications (message)
                VALUES(?)
            ");
            $stmt3->bind_param('s',$msg);
            if(!$stmt3->execute()){
                throw new Exception("Notification not sent");
            }

            $updatedColumns = implode(',', $updatedColumns);
            $valuesBefore = implode(',', $valuesBefore);
            $newValues = implode(',', $newValues);

            $action = 'Update';
            $actor = "{$_SESSION['name']} {$_SESSION['lname']}";
            $actor_id = "{$_SESSION['id']}";
            $stmt7 = $this->conn->prepare("
                INSERT INTO logs(action, columns_updated, actor, values_before, values_after, actor_id)
                VALUES(?,?,?,?,?,?)
            ");
            $stmt7->bind_param('ssssss', $action, $updatedColumns, $actor, $valuesBefore, $newValues, $actor_id);
            if(!$stmt7->execute()){
                throw new Exception("log not maintained");
            }

            $this->conn->commit();
            jsuccess("Employee updated");
        }
        catch(Exception $e){
            $this->conn->rollback();
            jerror("Employee not updated" . $e);
        }
    }

}