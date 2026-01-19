<?php include './layouts/sidebar.php' ?>

<?php include './layouts/topbar.php' ?>

<!-- Weekly Inactive Table -->
<div class="content">
    <div class="pr-3 pb-1" style="float:right;">
        <button class="goBack btn btn-primary btn-sm">Go Back</button>
    </div>
  <div class="m-3">
    <table id="empTable2" class="table table-dark table-bordered table-striped">
      <thead>
        <tr>
          <th class="code">Employee code</th>
          <th class="">First name</th>
          <th class="">Last name</th>
          <th class="">Email</th>
          <th class="">Designation</th>
          
        </tr>
      </thead>
      <tbody>

      </tbody>
    </table>
  </div>
</div>


<?php include './layouts/footer.php' ?>
<script>
  var myTable = $('#empTable2').DataTable({
    processing: true,
    serverSide: true,
    responsive: true,
    dom: 'Bfrtip',
    ajax: {
      url: "handleform.php",
      type: "POST",
      data: {action : "fetchWeeklyInactive"}
    },
    order: [],
    columns: [
      {data: "emp_id"},
      {data: "fname"},
      {data: "lname"},
      {data: "email"},
      {data: "designations"}
    ]
  });
  $(document).on('click','.goBack', function(){
    window.location.href = 'index.php';
  });
</script>