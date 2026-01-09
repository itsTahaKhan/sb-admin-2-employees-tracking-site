<?php
if($action==='downloadEmps'){
    $sql = ("SELECT * FROM employees");
    $result = $conn->query($sql);
    $headers = ['emp_id', 'fname', 'lname', 'email', 'design_name'];
    $fileDown = fopen('emplist.csv', 'w');
    fputcsv($fileDown, $headers);
    while($r = $result->fetch_assoc()){
        $row = [
            $id = $r['emp_id'], $fname = $r['fname'], $lname = $r['lname'], 
            $email = $r['email'], $design = $r['design_name'] 
        ];
        fputcsv($fileDown, $row);
    }
    fclose($fileDown);
    if(fopen('emplist.csv', 'r')){
        jsuccess('Downloading list');
    }
    else{
        jerror("Couldn't download list");
    }
}