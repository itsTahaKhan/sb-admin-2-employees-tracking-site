

<?php include './layouts/sidebar.php' ?>

<?php include './layouts/topbar.php' ?>

<div id="logsContainer" class="row">
</div>
<div class="modal fade" id="logModal">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Log Details</h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-6">
            <h6>Before</h6>
            <pre id="logBefore"></pre>
          </div>
          <div class="col-md-6">
            <h6>After</h6>
            <pre id="logAfter"></pre>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>


<?php include './layouts/footer.php' ?>

<script src="./js/logs.js"></script>