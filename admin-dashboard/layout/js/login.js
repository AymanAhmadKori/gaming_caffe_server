(async ()=>{
  // Get Authcode from url \\
  function getAuthCode() {
    const urlParams = new URLSearchParams(window.location.search);
    
    // Remove params from URL \\
    const newUrl = window.location.origin + window.location.pathname;
    window.history.replaceState(null, "", newUrl);
    
    return urlParams.get("code");
  }

  // Check if JWT exsists in localstorage
  let JWT = localStorage.getItem('JWT');
  if((await verifyJWT(JWT))) {
    // Go to booking page
    goToPage('index.html');
  }
  
  // Get code
  const authCode = getAuthCode();

  // Check if code exsists \\
  if(!authCode) return;

  let pageContent = document.querySelector('.pageContent');

  // Add loading class to pageContent
  pageContent.classList.add('loading');

  JWT = await fetch("http://localhost/projects/github/gaming_caffe_server/APIs/login.php", {
      method: "POST",
      body: JSON.stringify({
        code: authCode,
        admin: true
      })
  }).then(res => res.text());
    
  // Veryfi JWT
  if( !(await verifyJWT(JWT)) ) {
    // Remove loading class to pageContent
    pageContent.classList.remove('loading');
    
    // Push alert \\
    pushRealTimeAlert('error', trans('حدث خطأ. حاول مرة أخرى.'));
    return;
  }
    
  // Save JWT in localStorage & go to index page
  localStorage.setItem('JWT', JWT);
  goToPage('index.html');
})();