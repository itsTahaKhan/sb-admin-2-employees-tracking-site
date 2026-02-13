jQuery(document).ready(function($){
  loadDesignationOptions();
  loadUserRoles();
  loadDesignations();
  loadTotalUsers();
  setUsername();
  $("#emp_design_select").select2();
  $("#addUserDesignation").select2();
  loadNotifications();
});

  $.ajaxSetup({
    data: {
      csrf_token: CSRF_TOKEN
    }
  });

  var logs = $('#logsTable').DataTable({
    processing: true,
    serverSide: true,
    responsive: true,
    dom: 'Bfrtip',
    buttons: [{
      extend: "excelHtml5",
      text: "Download Logs",
      filename: "Logs",
      titleAttr: "Export to Excel",
      exportOptions: {
        columns: [0,1]
      }
    }],
    ajax: {
      url: "handleform.php",
      type: "POST",
      data: {action : "fetchLogs"}
    },
    order: [],
    columns: [
      {data: "created_at"},
      {data: "action"},
      {data: "actor"},
      {data: "columns_updated"},
      {data: "values_before"},
      {data: "values_after"}
    ]
  });

  var myTable = $('#empTable').DataTable({
    processing: true,
    serverSide: true,
    responsive: true,
    dom: 'Bfrtip',
    buttons: [{
      extend: "excelHtml5",
      text: "Download Employees List",
      filename: "empList",
      titleAttr: "Export to Excel",
      exportOptions: {
        columns: [0,1,2,3,4,5]
      }
    }],
    ajax: {
      url: "handleform.php",
      type: "POST",
      data: {action : "fetchEmployeesServer"}
    },
    order: [],
    columns: [
      {data: "emp_id"},
      {data: "fname"},
      {data: "lname"},
      {data: "email"},
      {data: "designations"},
      {data: "role"},
      {data: "action"}
    ]
  });

  var myTable1 = $('#hierarchyTable').DataTable({
    processing: true,
    serverSide: true,
    dom: 'Bfrtip',
    ajax: {
      url: "handleform.php",
      type: "POST",
      data: {action : "fetchHierarchy"}
    },
    order: [],
    columns: [
      {data: "names"},
      { data: "parent_names",
        render: function(data){
          return data === null ? 'Parent(no parent record)' : data;
        }
      },
    ]
  });

  function togglepass(){
    let input = document.getElementById('login_pass');
    if(input.type === 'password'){
      input.type = 'text';
    }
    else{
      input.type = 'password';
    }
  }

  function hideAll() { $('.forms').hide(); 
  }

  //------------load sidebar---------------
  function loadSidebar(){
    $.post('handleform.php', {action: 'fetchSidebar'}, html=>{
      
    });
  }

  //------Load Designations--------
  function loadDesignations(){ 
    $.post('handleform.php',{action:'fetchDesignations'},
     html=>$('#showDesignations tbody').html(html)); 
  }


  //-------- Load Designation Options For Employees--------
  function loadDesignationOptions(){
    $.post('handleform.php',{action:'fetchDesignationOptions'},
    html=>$('#addUserDesignation,#emp_design_select').html(html)); 
  }

  //-------------Load Roles Options ------------------
function loadUserRoles() {
  $.post('handleform.php', { action: 'fetchRolesOptions' }, function (html) {

    $('#addUserRole')
      .html(html)
      .select2({
        placeholder: "Select role",
        width: "100%",
        allowClear: true,
        dropdownParent: $('#modalAddEmployee'),
        minimumResultsForSearch: Infinity
      });

    $('#updateUserRole')
      .html(html)
      .select2({
        placeholder: "Select role",
        width: "100%",
        allowClear: true,
        dropdownParent: $('#empUpdateModal'),
        minimumResultsForSearch: Infinity
      });
  });
}



  //--------Set Username-------------
  function setUsername(){
    $.post('handleform.php', {action:'getUserName'}, resp=>{
    resp = JSON.parse(resp);
    $('#username').text(resp.username);
  });
  }

  // Add Designation
  $('#formAddDesignation').submit(function(e){
    e.preventDefault(); 
    $.post('handleform.php',{action:'addDesignation',design_name:$('#designationadd').val().trim()}, resp=>{
      if(resp.status==='success'){
        Swal.fire({
          icon: 'success',
          title: '!Success',
          text: JSON.stringify(resp.msg)
        });
      }
      else{
        Swal.fire({
          icon: 'error',
          title: '!Error',
          text: JSON.stringify(resp.msg)
        });
      }
      $('#designationadd').val(''); 
      loadDesignations(); 
      loadDesignationOptions();
    });
  });

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
              Swal.fire({
                icon: 'error',
                title: '!Error',
                text: resp.msg
              });
              myTable.ajax.reload(null, false);
            } 
            else {
              Swal.fire({
                icon: 'success',
                title: '!Success',
                text: resp.msg
              });
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

  //--------- Delete Designation Button------------
  $(document).on('click','.delDes',function(){
    if(!confirm('Delete designation "'+$(this).data('name')+'"?')) return;
    $.post('handleform.php',{action:'deleteDesignation',design_name:$(this).data('name')},resp=>{
      if(resp.status==='success'){
        Swal.fire({
          icon: 'success',
          title: '!Success',
          text: JSON.stringify(resp.msg)
        });
      }
      else{
        Swal.fire({
          icon: 'error',
          title: '!Error',
          text: JSON.stringify(resp.msg)
        });
      }
      loadDesignations(); 
      myTable.ajax.reload(null,false); 
      loadDesignationOptions();});
  });

  //---------Update designation button-----------
  $(document).on('click','.updDes',function(){
    $('#des_old_name').val($(this).data('name')); 
    $('#des_new_name').val($(this).data('name')); 
    $('#desUpdateModal').modal('show');
  });

  //---------Update designation form----------
  $('#formUpdateDesignation').submit(function(e){
    e.preventDefault(); 
    $.post('handleform.php',{action:'updateDesignation',old_name:$('#des_old_name').val(),new_name:$('#des_new_name').val().trim()}, resp=>{
      if(resp.status==='success'){
        Swal.fire({
          icon: 'success',
          title: '!Success',
          text: JSON.stringify(resp.msg)
        });
      }
      else{
        Swal.fire({
          icon: 'error',
          title: '!Error',
          text: JSON.stringify(resp.msg)
        });
      }
      $('#desUpdateModal').modal('hide'); 
      loadDesignations(); 
      myTable.ajax.reload(null,false); 
      loadDesignationOptions();
    });
  });

  // Delete Employee
  $(document).on('click','.delEmp',function(){
    if(!confirm('Delete employee ID '+$(this).data('id')+'?')) return;
    $.post('handleform.php',{action:'deleteEmployee',emp_id:$(this).data('id')}, resp=>{
      if(resp.status==='success'){
        Swal.fire({
          icon: 'success',
          title: '!Success',
          text: JSON.stringify(resp.msg)
        });
      }
      else{
        Swal.fire({
          icon: 'error',
          title: '!Error',
          text: JSON.stringify(resp.msg)
        });
      }
      myTable.ajax.reload(null,false);});
  });

  //-----------Employee update button-----------
  $(document).on('click','.updEmp',function(){
    $('#emp_orig_id').val($(this).data('id')); 
    $('#emp_id_in').val($(this).data('id')); 
    $('#emp_fname').val($(this).data('fname'));
    $('#emp_lname').val($(this).data('lname')); 
    $('#emp_email').val($(this).data('email')); 
    let roleId = String($(this).attr('data-role'));
    $('#updateUserRole').val(roleId).trigger('change');
    let des = $(this).data('designations');
    if (des) {
        let desArray = des.split(',');
        $('#emp_design_select').val(desArray).trigger('change');
    } else {
        $('#emp_design_select').val([]).trigger('change');
    }
    $('#empUpdateModal').modal('show');
  });

  //---------Employee update form-----------
   $('#formUpdateEmployee').submit(function(e){
    e.preventDefault(); 
    clearFormErrors();
    $.ajax({
        url: 'handleform.php',
        type: 'POST',
        data: $(this).serialize() + '&action=updateEmployee&csrf_token='+CSRF_TOKEN,
        dataType: 'json',
        success: (resp) => {
            if (resp.status === 'validationError') {
              showFormErrors('formUpdateEmployee',resp.msg);
            }
            else if(resp.status==='error'){
              Swal.fire({
                icon: 'error',
                title: '!Error',
                text: JSON.stringify(resp.msg)
              });
              myTable.ajax.reload(null, false);
            } 
            else {
              Swal.fire({
                icon: 'success',
                title: '!Success',
                text: JSON.stringify(resp.msg)
              });
              $(this)[0].reset();
              $('#empUpdateModal').modal('hide');
              $('#emp_design_select').val([]).trigger('change');
              myTable.ajax.reload(null, false);
              loadDesignationOptions();
            }
        }
    });
  });

  //---------Logout---------
  $(document).on('click', '#logout', function(e){
    e.preventDefault();
    $.post('handleform.php', {action:'logout'}, resp=>{
        if(resp.status==='success'){
          window.location.href = 'login.php';
        }
    });
  });

  //change pass modal clicker
  $(document).on('click', '#changePass', function(){
    $('#profileUpdateModal').modal('show');
  });

  //change pass form
  $('#formUpdateProfile').submit(function(e){
    e.preventDefault();
    $.post('handleform.php', $(this).serialize()+'&action=changePassword&csrf_token=' + CSRF_TOKEN , resp=>{
      if(resp.status==='success'){
        $('#profileUpdateModal').modal('hide');
        Swal.fire({
          icon: 'success',
          title: '!Success',
          text: JSON.stringify(resp.msg)
        });
        this.reset();
      }
      else{
        Swal.fire({
          icon: 'error',
          title: '!Error',
          text: JSON.stringify(resp.msg)
        });
        old_pass.reset();
        this.old_pass.reset();
      }
    });
  });

  //---------Employees File Uploading----------
  $('#fileUploadForm').submit(function(e){
    e.preventDefault();
    var fileData = $('#csvFile').prop('files')[0];
    var formData = new FormData();
    formData.append('csvFile', fileData);
    formData.append('action', 'fileUpload');
    formData.append('csrf_token', CSRF_TOKEN);
    $.ajax({
      url: 'handleform.php',
      type: 'post',
      data: formData,
      contentType: false,
      processData: false,
      success: function(resp){
        if(resp.status==='success'){
          Swal.fire({
            icon: 'success',
            title: '!Success',
            text: JSON.stringify(resp.msg)
          });
          $('#fileUploadModal').modal('hide');
          myTable.ajax.reload(null,false);
          loadDesignations();
          loadDesignationOptions();
        }
        else if(resp.status==='error'){
          // window.location.href = 'fixrows.csv';
          Swal.fire({
            icon: 'error',
            title: '!Error',
            text: JSON.stringify(resp.msg) + "fix the rows"
          });
          $("#fileUploadForm")[0].reset();
          $('#fileUploadModal').modal('hide');
          myTable.ajax.reload(null,false);
          loadDesignations();
          loadDesignationOptions();
        }
        else{
          Swal.fire({
            icon: 'error',
            title: '!Error',
            text: JSON.stringify(resp.msg)
          });
          myTable.ajax.reload(null,false);
          loadDesignations();
          loadDesignationOptions();
        }
      },
      error: function(resp){
        $(this)[0].reset();
        $('#fileUploadModal').modal('hide');
        myTable.ajax.reload(null,false);
        loadDesignations();
        loadDesignationOptions();
      }
    });
  });
  
  // hierarchial file upload
  $('#hierarchyForm').submit(function(e){
    e.preventDefault();
    var fileData = $('#hcsvFile').prop('files')[0];
    var formData = new FormData();
    formData.append('hcsvFile', fileData);
    formData.append('action', 'hfileupload');
    
    $.ajax({
      url: 'handleform.php',
      type: 'post',
      data: formData,
      contentType: false,
      processData: false,
      success: function(resp){
        if(resp.status==='success'){
          $('#hierarchyModal').modal('hide');       
          Swal.fire({
            icon: 'success',
            title: '!Success',
            text: JSON.stringify(resp.msg)
          });
          myTable1.ajax.reload(null, false);
          
        } 
      },
      error: function(resp){
        $(this)[0].reset();
        $('#fileUploadModal').modal('hide');
        myTable1.ajax.reload(null, false);
      }
    });
  });

// --------getTotalUsers--------
function loadTotalUsers (){
  $.post('handleform.php', {action:'getTotalUsers'} , resp=>{
    resp = JSON.parse(resp);
    $('#total_emp').text(resp.totalUsers);
  });
}

function loadNotifications(){
  $.ajax({
    url: 'handleform.php',
    type: 'post',
    dataType: 'json',
    data: { action: 'fetchNotifications'},
    success: resp => {

    if(resp.status==='success'){
      $('#notifCount').text(resp.unread);
      $('#notifCount2').text(" (" + resp.unread + ")");
      $('#notifScrollArea').empty();

      if(resp.data.length === 0){
        $('#notifScrollArea').html(
          '<span class="dropdown-item text-muted">No notifications</span>'
        );
        return;
      }

      resp.data.forEach(notif => {
        const cls = Number(notif.is_read) === 0 ? 'notif-unread' : 'notif-read';

        $('#notifScrollArea').append(`
          <a href="#"
             class="dropdown-item notif-item ${cls}"
             data-id="${notif.id}">
            ${notif.message}
            <span class="small text-muted d-block">${notif.created_at}</span>
          </a>
        `);
      });
    }
    else if(resp.status==='error'){
      $('#notifCount').text('0');
      $('#notifCount2').text(" (" + '0' + ")");
      $('#notifScrollArea').empty();
      $('#notifScrollArea').html(
        '<span class="dropdown-item text-muted">' + resp.msg + '</span>'
      );
    }
    },
  });
}

$("#notifList").on('click', '.notif-item', function(e){
  e.preventDefault();
  e.stopPropagation();
  let notif_id = $(this).data('id');
  $.post('handleform.php', {notif_id, action: 'markRead'}, ()=>{
    loadNotifications();
  });
  $(this).removeClass('notif-unread');
  $(this).addClass('notif-read');

  let c = parseInt($('#notifCount').text());
  if (c > 0) $('#notifCount').text(c - 1);
});

$(document).on('click', '#markAllRead', function(e){
  e.preventDefault();
  $.post('handleform.php', {action: 'allRead'}, ()=>{
    loadNotifications();
  })
});


// setInterval(loadNotifications,2000);