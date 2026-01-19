
const mediaQuery = window.matchMedia("(max-width:768px)");
const btn = document.getElementById('fileUploadBtn');

function handleMediaQueryChange(event){
  if(event.matches){
    btn.textContent = "Upload CSV";
  }
  else{
    btn.textContent = "Upload CSV file to add multiple employees";
  }
}

mediaQuery.addEventListener('change', handleMediaQueryChange);
handleMediaQueryChange(mediaQuery);