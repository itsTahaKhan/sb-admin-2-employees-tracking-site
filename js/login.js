  function togglepass(){
    let input = document.getElementById('login_pass');
    if(input.type === 'password'){
      input.type = 'text';
    }
    else{
      input.type = 'password';
    }
  }

      // login
  $('#loginForm').submit(function(e){
    e.preventDefault();
    $.post('handleform.php', $(this).serialize()+'&action=login', resp=>{
      
      if(resp.status==='success'){
        window.location.href = 'login.php';
        Swal.fire({
          icon: 'success',
          title: '!Success',
          text: JSON.stringify(resp.msg)
        });
        window.location.href = "index.php";
      }
      else{
        Swal.fire({
          icon: 'error',
          title: '!Error',
          text: JSON.stringify(resp.msg)
        });
      }
    });
  });