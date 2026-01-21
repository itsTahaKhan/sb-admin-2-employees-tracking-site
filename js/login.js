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
      if(resp.status==='admin'){
        window.location.href = 'index.php';
        alert(JSON.stringify(resp.msg));
      }
      else if(resp.status==='employee'){
        window.location.href = 'login.php';
        alert(JSON.stringify(resp.msg));
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