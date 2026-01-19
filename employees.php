<?php include './layouts/sidebar.php' ?>

<?php include './layouts/topbar.php' ?>

<style>
  .is-invalid {
    border-color: #dc3545 !important;
  }

  .is-invalid:focus {
    box-shadow: 0 0 0 0.1rem rgba(220, 53, 69, 0.25);
  }
</style>

<!-- Employees Table -->  
<div class="content">
  <div class="m-3">
    <div class="row">
      <button id="fileUploadBtn" class="btn btn-success btn-sm" style="margin-bottom:5px;" data-toggle="modal" data-target="#fileUploadModal">Add (.csv) file to add multiple employees.</button>
      <button id="addEmpBtn" class="btn btn-sm btn-success mb-2" style="margin-left:1vw;" data-toggle="modal" data-target="#modalAddEmployee">Add Employee</button>
    </div>
    <table id="empTable" class="table table-dark table-bordered table-striped">
      <thead>
        <tr>
          <th class="code">Employee code</th>
          <th class="fname">First name</th>
          <th class="lname">Last name</th>
          <th class="email">Email</th>
          <th class="designation">Designation</th>
          <th class="action">Action</th>
        </tr>
      </thead>
      <tbody>

      </tbody>
    </table>
  </div>
</div>


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
            <select style="width:220px; height:20px;" name="role" id="updateUserRole" class="form-control"></select>
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
<!-- Add multiple employees -->
<div class="modal fade" id="fileUploadModal" tabindex="-1">
  <div class="modal-dialog">
    <form id="fileUploadForm" class="modal-content" enctype="multipart/form-data">
      <div class="modal-header"><h5 class="modal-title">Upload (.csv) file</h5><button type="button" class="close" data-dismiss="modal">&times;</button></div>
      <div class="modal-body">
        <div class="form-row">
          <div class="form-group colmd-12">
            <label for="csvFile">Upload the file here</label>
            <input type="file" id="csvFile" name="csvFile" class="form-control-file" accept=".csv">
          </div>
        </div>
      </div>
      <div id="upload" class="modal-footer"><button class="btn btn-success mt-2">Upload</button></div>
    </form>
  </div>
</div>

<script src="js\employees.js"></script>

<?php include './layouts/footer.php' ?>