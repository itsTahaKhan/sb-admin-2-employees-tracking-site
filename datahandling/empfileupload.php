<?php

function uploadError($msg) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['status' => 'uploadError', 'msg' => $msg]);
    exit;
}

//--------------------File Uploading-----------------------
if($action==='fileUpload'){
    if(!isset($_FILES['csvFile'])){
        uploadError('Upload a file first');
        exit;
    }
    else{
        $headerExpected = array('emp_id', 'fname', 'lname', 'email', 'design_name', 'pass');
        if($_FILES['csvFile']['error'] === 0){
            $file = fopen($_FILES['csvFile']['tmp_name'], 'r');
            $headers = fgetcsv($file);
            $headers = array_map('strtolower', $headers);
            if(array_diff($headers, $headerExpected)){
                jerror('Unidentified headers format');
                exit;
            }
            $designations = [];
            $sql = "SELECT design_name FROM designations";
            $res = $conn->query($sql);
            while($r = $res->fetch_assoc()){
                $designations[] = $r['design_name'];
            }
            $ids = [];
            $emails = [];
            $sql = "SELECT * FROM employees";
            $res = $conn->query($sql);
            while($r = $res->fetch_assoc()){
                $ids[] = $r['emp_id'];
                $emails[] = trim($r['email']);
            }
            $allinserted = true;
            $addCounter = 0;
            $leftCounter = 0;
            $rowcounter = 0;
            $missedRows = [];
	    	$wrong_designations = '';
            while($data = fgetcsv($file, 500, ",")){
                $row = array_combine($headers, $data);
                $file_id = $row['emp_id'];
                $file_fname = ucwords($row['fname']);
                $file_lname = ucwords($row['lname']);
                $file_email = $row['email'];
                $file_design_name = $row['design_name'];
                $file_design_name = array_map('trim', explode('|', $file_design_name));
                $file_pass = $row['pass'];
				$continue = false;
                foreach($file_design_name as $des){
					if(!in_array($des, $designations)){
						$continue = true;
					}
				}
                if($file_design_name==='' || $file_id==='' || $file_fname==='' || $file_lname==='' || $file_email==='' || 
        	        in_array($file_email,$emails) ||
					$continue === 'true' ||
                	in_array($file_id,$ids) || 
	                !filter_var($file_email,FILTER_VALIDATE_EMAIL)){
						$wrong_designations = implode('|',$file_design_name);
    	                $missedRows[$rowcounter] = [$file_id, $file_fname, $file_lname, $file_email, $wrong_designations, $file_pass];
        	            $leftCounter++;
            	        $rowcounter++;
                	    continue;
                }
                $hashed = password_hash($file_pass, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("
                    INSERT INTO 
                    employees (emp_id, fname, lname, email, pass, created_at) 
                    VALUES(?,?,?,?,?,?) "
                );
                $stmt->bind_param(
                    'isssss',
                    $file_id,$file_fname,$file_lname,$file_email,$hashed,$date
		);  
                if($stmt->execute()){
                    foreach($file_design_name as $des){
                        $stmt1 = $conn->prepare("
                        INSERT INTO 
                            employeedesignations(emp_id, design_name)
                            VALUES(?,?)"
                        );
                	$stmt1->bind_param('is', $file_id,$des);
                        if($stmt1->execute()){            
                            $addCounter++;
                            $rowcounter++;
                        }
                    }


                }
            }
            $newFile = fopen('fixrows.csv', 'w');
            fputcsv($newFile, $headerExpected);
            foreach($missedRows as $row){
                fputcsv($newFile,$row,',');
            }
            fclose($newFile);
            
            if($addCounter==$rowcounter){
                jsuccess($addCounter . ' rows added.');
            }
            
            elseif($addCounter>0 && $addCounter<$rowcounter){
                jerror($addCounter . ' rows added and ' . $leftCounter . ' rows left.');
            }
            
            else{
                jerror('Invalid rows input. ');
            }
            fclose($file);
        }
        else{
            uploadError('Some error occurred while uploading file');
        }
    }
}

?>