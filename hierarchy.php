<?php include './layouts/sidebar.php' ?>

<?php include './layouts/topbar.php' ?>

<div class="content">
  <div class="m-3">
    <table id="hierarchyTable" class="table table-dark table-bordered table-striped">
      <thead>
        <tr>
          <th class="names">Name</th>
          <th class="parent_id">Parent Name</th>
        </tr>
      </thead>
      <tbody>
      </tbody>
    </table>
  </div>
</div>

<div class="form-row">
<button class="btn btn-success btn-sm mb-2" style="margin-left:20px; float:left;" data-toggle="modal" data-target="#hierarchyModal">Add the file.</button>
</div>
<div class="modal fade" id="hierarchyModal" tabindex="-1">
  <div class="modal-dialog">
    <form id="hierarchyForm" class="modal-content" enctype="multipart/form-data">
      <div class="modal-header"><h5 class="modal-title">Upload file</h5><button type="button" class="close" data-dismiss="modal">&times;</button></div>
      <div class="modal-body">
        <div class="form-row">
          <div class="form-group colmd-12">
            <label for="csvFile">Upload the file here</label>
            <input type="file" id="hcsvFile" name="hcsvFile" class="form-control-file" accept=".csv">
          </div>
        </div>
      </div>
      <div id="upload" class="modal-footer"><button class="btn btn-success mt-2">Upload</button></div>
    </form>
  </div>
</div>

<?php include './layouts/footer.php' ?>