<!-- Add Employee Modal -->
<div class="modal fade" id="modalAddEmployee" tabindex="-1">
  <div class="modal-dialog">
    <form id="formAddUser" class="modal-content">
      <div class="modal-header"><h5 class="modal-title">Add Employee</h5><button type="button" class="close" data-dismiss="modal">&times;</button></div>
      <div class="modal-body">
        <div class="form-row">
          <div class="form-group col-md-4">
            <label>Employee code:<span style="color:red;">*</span> </label>
            <input class="form-control" name="emp_id" type="number" >
            <small class="text-danger error-text" data-error-for="emp_id"></small>
          </div>
          <div class="form-group col-md-4">
            <label>First name:<span style="color:red;">*</span> </label>
            <input class="form-control" name="fname" type="text" >
            <small class="text-danger error-text" data-error-for="fname"></small>
          </div>
          <div class="form-group col-md-4">
            <label>Last name:<span style="color:red;">*</span> </label>
            <input class="form-control" name="lname" type="text" >
            <small class="text-danger error-text" data-error-for="lname"></small>
          </div>
        </div>
        <div class="form-row">
          <div class="form-group col-md-6">
            <label>Email:<span style="color:red;">*</span></label>
            <input placeholder="example@gmail.com" class="form-control" name="email" type="text" >
            <small class="text-danger error-text" data-error-for="email"></small>
          </div>
          <div class="form-group col-md-6">
            <label>Designation:<span style="color:red;">*</span></label>
            <select  multiple="multiple" style="width:220px; height:25px;" name="designation[]" id="addUserDesignation" class="form-control"></select>
            <small class="text-danger error-text" data-error-for="designation"></small>
          </div>
      </div>
      <div class="form-row">
        <div class="form-group col-md-6">
          <label>Password: <span style="color:red;">*</span></label>
          <input class="form-control" type="password" name="pass" id="emp-pass">
          <small class="text-danger error-text" data-error-for="pass"></small>
        </div>
        <div class="form-group col-md-6">
          <label>Role: <span style="color:red;">*</span></label>
          <select style="width:220px; height:20px;" name="role" id="addUserRole" class="form-control"></select>
          <small class="text-danger error-text" data-error-for="role"></small>
        </div>
      </div>
      </div>
      <div class="modal-footer"><button class="btn btn-secondary" data-dismiss="modal">Cancel</button><button class="btn btn-primary" type="submit">Add</button></div>
    </form>
  </div>
</div>

<!-- Update Employee Modal -->
<div class="modal fade" id="empUpdateModal" tabindex="-1">
  <div class="modal-dialog">
    <form id="formUpdateEmployee" class="modal-content">
      <div class="modal-header"><h5 class="modal-title">Update Employee</h5><button type="button" class="close" data-dismiss="modal">&times;</button></div>
      <div class="modal-body">
        <input type="hidden" id="emp_orig_id" name="orig_emp_id">
        <div class="form-row">
          <div id="emp_code" class="form-group col-md-4">
            <label>Employee code:<span style="color:red;">*</span></label>
            <input id="emp_id_in" name="emp_id" class="form-control" type="number" >
            <small class="text-danger error-text" data-error-for="emp_id"></small>
          </div>
          <div class="form-group col-md-4">
            <label>First name:<span style="color:red;">*</span></label>
            <input id="emp_fname" name="fname" class="form-control" type="text" >
            <small class="text-danger error-text" data-error-for="fname"></small>
          </div>
          <div class="form-group col-md-4">
            <label>Last name:<span style="color:red;">*</span></label>
            <input id="emp_lname" name="lname" class="form-control" type="text" >
            <small class="text-danger error-text" data-error-for="lname"></small>
          </div>
        </div>
        <div class="form-row">
          <div class="form-group col-md-6">
            <label>Role: <span style="color:red;">*</span></label>
            <select style="width:220px; height:20px;" size="1" name="role" id="updateUserRole" class="form-control"></select>
            <small class="text-danger error-text" data-error-for="role"></small>
          </div>
          <div class="form-group col-md-6">
            <label>Designation:<span style="color:red;">*</span></label>
            <select multiple="multiple" style="width:220px; height:20px;" id="emp_design_select" name="designation[]" class="form-control" ></select>
            <small class="text-danger error-text" data-error-for="designation"></small>
          </div>
        </div>
        <div class="form-row">
          <div class="form-group col-md-6">
            <label>Email:<span style="color:red;">*</span></label>
            <input id="emp_email" name="email" class="form-control" type="email" >
            <small class="text-danger error-text" data-error-for="email"></small>
          </div>
        </div>
      </div>
      <div class="modal-footer"><button class="btn btn-secondary" data-dismiss="modal">Cancel</button><button class="btn btn-primary" type="submit">Save</button></div>
    </form>
  </div>
</div>

jQuery(document).ready(function($){
  loadDesignationOptions();
  loadUserRoles();
  loadDesignations();
  loadTotalUsers();
  setUsername();
  $("#emp_design_select").select2();
  $("#addUserDesignation").select2();
  $("#addUserRole").select2({
    placeholder: "Select role",
    width: "100%",
    allowClear: true
  });
  $("#updateUserRole").select2({
    placeholder: "Select role",
    width: "100%",
    allowClear: true
  });
  loadNotifications();
});

  //-------------Load Roles Options ------------------
  function loadUserRoles(){
    $.post('handleform.php', {action:'fetchRolesOptions'}, 
      html=>{$('#addUserRole,#updateUserRole').html(html);}
    );
  }
  // Add Employee
$('#formAddUser').submit(function (e) {
    e.preventDefault();
    // clear previous errors
    clearFormErrors();

    $.ajax({
        url: 'handleform.php',
        type: 'POST',
        data: $(this).serialize() + '&action=addEmployee&csrf_token='+CSRF_TOKEN,
        dataType: 'json',
        success: (resp) => {

            if (resp.status === 'validationError') {
              showFormErrors('formAddUser',resp.msg);
            }
            else if(resp.status==='error'){
              alert(resp.msg);
              myTable.ajax.reload(null, false);
            } 
            else {
              alert(resp.msg);
              this.reset();
              $('#addUserDesignation').val([]).trigger('change');
              $('#addUserRole').val([]).trigger('change');
              myTable.ajax.reload(null, false);
            }
        }
    });
});

$('#modalAddEmployee').on('hidden.bs.modal', function(e){
  $('.is-invalid').removeClass('is-invalid');
  $('.error-text').text('');

  // clear select2 styling
  $('.select2-selection').removeClass('is-invalid');
});

$('#empUpdateModal').on('hidden.bs.modal', function(e){
  $('.is-invalid').removeClass('is-invalid');
  $('.error-text').text('');

  // clear select2 styling
  $('.select2-selection').removeClass('is-invalid');
});

function showFormErrors(formId, errors) {
    for (let field in errors) {
        let input = $('#' + formId + ' [name="' + field + '"]');
        let errorBox = $('#' + formId + ' [data-error-for="' + field + '"]');
        if (input.length) {
            input.addClass('is-invalid');
        }

        if (errorBox.length) {
            errorBox.text(errors[field][0]);
        }

        //For select2 dropdown
        if (field === 'designation') {
            input.next('.select2-container').find('.select2-selection').addClass('is-invalid');
        }
        if (field==='role'){
            input.next('.select2-container').find('.select2-selection').addClass('is-invalid');
        }
    }
}

function clearFormErrors() {
    $('.is-invalid').removeClass('is-invalid');
    $('.error-text').text('');

    // clear select2 styling
    $('.select2-selection').removeClass('is-invalid');
  }
$('input, select').on('input change', function () {
    $(this).removeClass('is-invalid');
    $('[data-error-for="' + this.name.replace('[]', '') + '"]').text('');
});

<?php 
if($action==='fetchRolesOptions'){
    $res = $conn->query("
        SELECT role_name, role_id FROM roles ORDER BY role_name
    ");
    echo("<optgroup>  ------Select Role----");
    if (!$res || $res->num_rows === 0) {
        echo "<option value=''>No designations</option>";
        exit;
    }
    while ($row = $res->fetch_assoc()) {
        $value = $row['role_id'];
        $data = htmlspecialchars(ucwords($row['role_name']), ENT_QUOTES);
        echo "<option value={$value}>{$data}</option>";
    }
    echo"</optgroup>";
    exit;
}


if ($action === 'fetchEmployeesServer') {

    $start  = $_POST['start'] ?? 0;      
    $length = $_POST['length'] ?? 10;    
    $search = $_POST['search']['value'] ?? '';

    $where = "";
    $params = [];
    $types = "";

    if ($search !== "") {
        $where = "
            WHERE e.fname LIKE ?
               OR e.lname LIKE ?
               OR e.email LIKE ?
               OR e.emp_id LIKE ?
               OR ed.design_name LIKE ?
        ";

        $searchLike = "%$search%";
        $params = [$searchLike, $searchLike, $searchLike, $searchLike, $searchLike];
        $types = "sssss";
    }

    $totalRes = $conn->query("SELECT COUNT(*) AS cnt FROM employees");
    $total = $totalRes->fetch_assoc()['cnt'];

    $query = "
        SELECT e.emp_id, e.fname, e.lname, e.email, e.role,
           GROUP_CONCAT(ed.design_name) AS designations
        FROM employees e
        LEFT JOIN employeedesignations ed ON e.emp_id = ed.emp_id
        $where
        GROUP BY e.emp_id
        ORDER BY e.emp_id
        LIMIT ?, ?
    ";

    $stmt = $conn->prepare($query);

    if ($search !== "") {
        $types .= "ii";
        $params[] = $start;
        $params[] = $length;
        $stmt->bind_param($types, ...$params);
    } 
    else {
        $stmt->bind_param("ii", $start, $length);
    }

    $stmt->execute();
    $dataRes = $stmt->get_result();


    $rows = [];
while ($row = $dataRes->fetch_assoc()) {
    $rows[] = [
        "emp_id"      => (int)$row['emp_id'],
        "fname"       => $row['fname'],
        "lname"       => $row['lname'],
        "email"       => $row['email'],
        "designations"=> $row['designations'],
        "role" => $row['role'],
        "action"      => "
            <button style='margin:2px;' class='btn btn-sm btn-info updEmp' 
                data-id='{$row['emp_id']}'
                data-fname='{$row['fname']}'
                data-lname='{$row['lname']}'
                data-email='{$row['email']}'
                data-role='{$row['role']}'
                data-designations='{$row['designations']}'>Update</button>
                
        <button style='margin:2px;' class='btn btn-sm btn-danger delEmp'
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


  //-----------Employee update button-----------
  $(document).on('click','.updEmp',function(){
    $('#emp_orig_id').val($(this).data('id')); 
    $('#emp_id_in').val($(this).data('id')); 
    $('#emp_fname').val($(this).data('fname'));
    $('#emp_lname').val($(this).data('lname')); 
    $('#emp_email').val($(this).data('email')); 
    $('#updateUserRole').val($(this).data('role'));
    let des = $(this).data('designations');
    if (des) {
        let desArray = des.split(',');
        $('#emp_design_select').val(desArray).trigger('change');
    } else {
        $('#emp_design_select').val([]).trigger('change');
    }
    $('#empUpdateModal').modal('show');
  });
