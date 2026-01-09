<?php include './layouts/sidebar.php' ?>

<?php include './layouts/topbar.php' ?>

<div class="content">
<!-- Designations Table -->
  <div id="showDesignations" class="m-3 forms">
    <div class="mb-2 text-right">
      <button class="btn btn-sm btn-success" data-toggle="modal" data-target="#modalAddDesignation">Add Designation</button>
    </div>
    <table class="table table-bordered table-striped">
      <thead><tr><th>Designation Name</th><th style="width:200px;">Action</th></tr></thead>
      <tbody></tbody>
    </table>
  </div>
</div>

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


<?php include './layouts/footer.php' ?>