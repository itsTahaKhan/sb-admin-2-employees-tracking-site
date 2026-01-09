<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>JStask1</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<style>
body { font-family: Arial, sans-serif; }
.sidebar { position: fixed; top:0; left:0; width:220px; height:100%; background:#343a40; padding-top:60px; font-size:20px; }
.sidebar a { display:block; color:white; padding:12px 16px; text-decoration:none; }
.sidebar a:hover { background:#495057; color:white; }
.content { margin-left:220px; padding:20px; }
.jumbotron { margin-bottom:0; padding:0 20px; }
.table-actions > button { margin-right:6px; }
</style>
</head>
<body>

<div class="jumbotron text-center"><h4 style="float:left; margin-left:220px; margin-top:20px;">Data manipulation</h4></div>

<div class="sidebar">
  <a href="#" id="linkShowDesign">Show Designations</a>
  <a href="#" id="linkShowEmp">Show Employees</a>
</div>

<div class="content">
  <!-- Designations Table -->
  <div id="showDesignations" class="forms" style="display:none;">
    <div class="mb-2 text-right">
      <button class="btn btn-sm btn-success" data-toggle="modal" data-target="#modalAddDesignation">Add Designation</button>
    </div>
    <table class="table table-bordered table-striped">
      <thead><tr><th>Designation Name</th><th style="width:200px;">Action</th></tr></thead>
      <tbody></tbody>
    </table>
  </div>

  <!-- Employees Table -->
  <div id="showEmp" class="forms" style="display:none;">
    <div class="mb-2 text-right">
      <button class="btn btn-sm btn-success" data-toggle="modal" data-target="#modalAddEmployee">Add Employee</button>
    </div>
    <table class="table table-bordered table-striped">
      <thead>
        <tr>
          <th>Employee code</th><th>First name</th><th>Last name</th><th>Email</th><th>Designation</th><th style="width:220px;">Action</th>
        </tr>
      </thead>
      <tbody></tbody>
    </table>
  </div>
</div>

<!-- Modals -->

<!-- Add Designation Modal -->
<div class="modal fade" id="modalAddDesignation" tabindex="-1">
  <div class="modal-dialog">
    <form id="formAddDesignation" class="modal-content">
      <div class="modal-header"><h5 class="modal-title">Add Designation</h5><button type="button" class="close" data-dismiss="modal">&times;</button></div>
      <div class="modal-body">
        <div class="form-group">
          <label>Designation name:<span style="color:red;">*</span></label>
          <input type="text" class="form-control" id="designationadd" name="designationadd" >
        </div>
      </div>
      <div class="modal-footer"><button class="btn btn-secondary" data-dismiss="modal">Cancel</button><button class="btn btn-primary" type="submit">Add</button></div>
    </form>
  </div>
</div>

<!-- Add Employee Modal -->
<div class="modal fade" id="modalAddEmployee" tabindex="-1">
  <div class="modal-dialog">
    <form id="formAddUser" class="modal-content">
      <div class="modal-header"><h5 class="modal-title">Add Employee</h5><button type="button" class="close" data-dismiss="modal">&times;</button></div>
      <div class="modal-body">
        <div class="form-row">
          <div class="form-group col-md-4"><label>Employee code:<span style="color:red;">*</span> </label><input class="form-control" name="emp_id" type="number" ></div>
          <div class="form-group col-md-4"><label>First name:<span style="color:red;">*</span> </label><input class="form-control" name="fname" type="text" ></div>
          <div class="form-group col-md-4"><label>Last name:<span style="color:red;">*</span> </label><input class="form-control" name="lname" type="text" ></div>
        </div>
        <div class="form-row">
          <div class="form-group col-md-6"><label>Email:<span style="color:red;">*</span></label><input class="form-control" name="email" type="email" ></div>
          <div class="form-group col-md-6">
            <label>Designation:<span style="color:red;">*</span></label>
            <select name="designation" id="addUserDesignation" class="form-control" ></select>
          </div>
      </div>
      </div>
      <div class="modal-footer"><button class="btn btn-secondary" data-dismiss="modal">Cancel</button><button class="btn btn-primary" type="submit">Add</button></div>
    </form>
  </div>
</div>

<!-- Update Designation Modal -->
<div class="modal fade" id="desUpdateModal" tabindex="-1">
  <div class="modal-dialog">
    <form id="formUpdateDesignation" class="modal-content">
      <div class="modal-header"><h5 class="modal-title">Update Designation</h5><button type="button" class="close" data-dismiss="modal">&times;</button></div>
      <div class="modal-body">
        <input type="hidden" id="des_old_name" name="old_name">
        <div class="form-group"><label>New name:<span style="color:red;">*</span></label><input id="des_new_name" name="new_name" class="form-control" ></div>
      </div>
      <div class="modal-footer"><button class="btn btn-secondary" data-dismiss="modal">Cancel</button><button class="btn btn-primary" type="submit">Save</button></div>
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
          <div class="form-group col-md-4"><label>Employee code:<span style="color:red;">*</span></label><input id="emp_id_in" name="emp_id" class="form-control" type="number" ></div>
          <div class="form-group col-md-4"><label>First name:<span style="color:red;">*</span></label><input id="emp_fname" name="fname" class="form-control" type="text" ></div>
          <div class="form-group col-md-4"><label>Last name:<span style="color:red;">*</span></label><input id="emp_lname" name="lname" class="form-control" type="text" ></div>
        </div>
        <div class="form-group"><label>Email:<span style="color:red;">*</span></label><input id="emp_email" name="email" class="form-control" type="email" ></div>
        <div class="form-group"><label>Designation:<span style="color:red;">*</span></label><select id="emp_design_select" name="designation" class="form-control" ></select></div>
      </div>
      <div class="modal-footer"><button class="btn btn-secondary" data-dismiss="modal">Cancel</button><button class="btn btn-primary" type="submit">Save</button></div>
    </form>
  </div>
</div>

<script>
  function hideAll() { $('.forms').hide(); }
  function loadDesignations(){ 
    $.post('handleform.php',{action:'fetchDesignations'},
     html=>$('#showDesignations tbody').html(html)); }
  function loadEmployees(){ 
    $.post('handleform.php',{action:'fetchEmployees'},
     html=>$('#showEmp tbody').html(html)); }
  function loadDesignationOptions(){
     $.post('handleform.php',{action:'fetchDesignationOptions'},
      html=>$('#addUserDesignation,#emp_design_select').html(html)); }

  // sidebar clicks
  $('#linkShowDesign').click(e=>{e.preventDefault(); hideAll(); $('#showDesignations').show(); loadDesignations(); loadDesignationOptions();});
  $('#linkShowEmp').click(e=>{e.preventDefault(); hideAll(); $('#showEmp').show(); loadEmployees(); loadDesignationOptions();});

  // Add Designation
  $('#formAddDesignation').submit(function(e){
    e.preventDefault(); 
    $.post('handleform.php',{action:'addDesignation',design_name:$('#designationadd').val().trim()}, resp=>{
      alert(JSON.stringify(resp)); $('#designationadd').val(''); loadDesignations(); loadDesignationOptions();
    });
  });

  // Add Employee
  $('#formAddUser').submit(function(e){
    e.preventDefault(); 
    $.post('handleform.php', $(this).serialize()+'&action=addEmployee', resp=>{
      alert(JSON.stringify(resp)); this.reset(); loadEmployees();
    });
  });

  // Delete/Update Designation
  $(document).on('click','.delDes',function(){
    if(!confirm('Delete designation "'+$(this).data('name')+'"?')) return;
    $.post('handleform.php',{action:'deleteDesignation',design_name:$(this).data('name')},resp=>{alert(JSON.stringify(resp)); loadDesignations(); loadEmployees(); loadDesignationOptions();});
  });
  $(document).on('click','.updDes',function(){
    $('#des_old_name').val($(this).data('name')); $('#des_new_name').val($(this).data('name')); $('#desUpdateModal').modal('show');
  });
  $('#formUpdateDesignation').submit(function(e){
    e.preventDefault(); 
    $.post('handleform.php',{action:'updateDesignation',old_name:$('#des_old_name').val(),new_name:$('#des_new_name').val().trim()}, resp=>{
      alert(JSON.stringify(resp)); $('#desUpdateModal').modal('hide'); loadDesignations(); loadEmployees(); loadDesignationOptions();
    });
  });

  // Delete/Update Employee
  $(document).on('click','.delEmp',function(){
    if(!confirm('Delete employee ID '+$(this).data('id')+'?')) return;
    $.post('handleform.php',{action:'deleteEmployee',emp_id:$(this).data('id')}, resp=>{alert(JSON.stringify(resp)); loadEmployees();});
  });
  $(document).on('click','.updEmp',function(){
    $('#emp_orig_id').val($(this).data('id')); $('#emp_id_in').val($(this).data('id')); $('#emp_fname').val($(this).data('fname'));
    $('#emp_lname').val($(this).data('lname')); $('#emp_email').val($(this).data('email')); $('#emp_design_select').val($(this).data('design'));
    $('#empUpdateModal').modal('show');
  });
  $('#formUpdateEmployee').submit(function(e){
    e.preventDefault(); 
    $.post('handleform.php', $(this).serialize()+'&action=updateEmployee', resp=>{
      alert(JSON.stringify(resp)); $('#empUpdateModal').modal('hide'); loadEmployees(); loadDesignationOptions();
    });
  });

</script>
</body>
</html>
