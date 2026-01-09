<?php 
class Employee{
    public $emp_id = null;
    public $fname = '';
    public $lname = '';
    public $email = '';
    public $designation = [];
    public $pass = '';
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
            $stmt1 = $this->conn->prepare("INSERT INTO employees (emp_id, fname, lname, email, pass, created_at) VALUES (?,?,?,?,?,?)");
            $stmt1->bind_param('isssss', $this->emp_id, $this->fname, $this->lname, $this->email, $hashpass, $date);
            if(!$stmt1->execute()){
                throw new Exception("Adding employee failed");
            }
            $stmt = $this->conn->prepare("INSERT INTO employeedesignations (emp_id, design_name) VALUES (?, ?)");
            foreach($this->designation as $des){
                $stmt->bind_param('is', $this->emp_id,$des);
                if(!$stmt->execute()){
                    throw new Exception("Adding designation failed");
                }
                
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

            $stmt1 = $this->conn->prepare("DELETE FROM employeedesignations WHERE emp_id = ?");
            $stmt1->bind_param('i', $this->orig);
            if(!$stmt1->execute()){
                throw new Exception("Designations not deleted");
            }

            $stmt = $this->conn->prepare("UPDATE employees SET emp_id = ?, fname = ?, lname = ?, email = ? WHERE emp_id = ?");
            $stmt->bind_param('isssi', $this->emp_id, $this->fname, $this->lname, $this->email, $this->orig);    
            if(!$stmt->execute()){
                throw new Exception("Employee table not updated");
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
            
            $this->conn->commit();
            jsuccess("Employee updated");
        }
        catch(Exception $e){
            $this->conn->rollback();
            jerror("Employee not updated");
        }
    }

}