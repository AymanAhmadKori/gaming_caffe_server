const navContainer = document.querySelector('nav#navContainer');
if(navContainer) { (async () =>{
  // Get navBar tempalte 
  let res = await fetchFile('templates/navBar.html');
  navContainer.innerHTML = res; // Insert navBar into navBar container 

  // get navBar element \\
  const navBar = navContainer.querySelector("nav#navBarElement");
  
  let mobileBtnsContainer = navBar.querySelector('.mobile-btns');
  let menuToggle = mobileBtnsContainer.querySelector('button.menu');
  let settingsBtn = navContainer.querySelector('.navLinks button.settings');
  // let notsBtn = navBar.querySelector('button.notificationsBtn');
  

  // Set active class if the link is active
  let linksContainer = navBar.querySelector('.navLinks');
  let links = [...linksContainer.querySelectorAll('a')];

  links.forEach(link =>{
    let isActive = link.getAttribute('isActive');
    if(isActive == pageTitle) {
      link.classList.add('active');
      link.removeAttribute('href');
    }
  });

  // Set navbar container height to navbar height
  let navBarHeight = getComputedStyle(navBar).height;
  navBar.style.setProperty('--navBar-height',navBarHeight);

  document.body.style.paddingTop = navBarHeight;

  // Hidden navBar on scroll \\
  let lastScrollY = window.scrollY;

  window.addEventListener('scroll', ()=>{
    let currentScrollY = window.scrollY;

    if(lastScrollY > currentScrollY) {
      navContainer.classList.remove('nav-hidden');
    } else {
      navContainer.classList.add('nav-hidden');
      menuHandel('inActive');
    }

    lastScrollY = currentScrollY;
  })

  // Settings button to mobile btns container
  function placeSettingsBtn(){
    if(window.innerWidth < 729) {
      mobileBtnsContainer.insertBefore(settingsBtn, menuToggle);
    } else {
      navBar.querySelector('.navBtns').append(settingsBtn)
    }
  }
  window.addEventListener('resize', placeSettingsBtn);

  // Menu toggle \\
  let linksTransition = getComputedStyle(linksContainer).transition.slice(0,-1) * 1000; // ms
  let canDo = true;
  function menuHandel(status = 'switch') {
    if(!canDo) return;
    function active(){
      canDo = false;
      navBar.classList.add('active');
      setTimeout(() => {
        linksContainer.classList.add('show');
      }, 2);        
      setTimeout(() => {
        canDo = true;
      }, linksTransition);
    }
    function inActive() {
      canDo = false;
      linksContainer.classList.remove('show');
      setTimeout(() => {
        navBar.classList.remove('active');
        canDo = true;
      }, linksTransition)
    }
    
    switch (status) {
      case "active" : active();
      break;
      case "inActive": inActive();
      break;
      default: 
      if(navBar.className.includes('active')) {inActive()}
      else {active()}  
    }
    
  }

  menuToggle.addEventListener('click', menuHandel)
  placeSettingsBtn()


  // === Settings window === \\
  function settingsHandel() {
    settingsBtn.classList.add('bgc-primary'); // Add primary background color to settings toggle \\

    let overlayElement = document.createElement('div');
    overlayElement.classList.add("overlayElement", "flex", "align-center", "just-center");
    
    let settingsElement = document.createElement('div');
    settingsElement.classList.add('settingsElement');

    // Append Header \\
    settingsElement.innerHTML = `
      <header class="flex align-center just-between">
        <div class="flex align-center gap-5">
          <i- type="gear" class="medium"></i->
          <h5><trans->الإعدادات</trans-></h5>
        </div>
        <button id="closeWindowBtn" class="medium bgc">
          <i- type="remove-x"></i->
        </button>
      </header>
    `;

    // Display settings group \\
    let themesSettings = `
      <div class="flex column gap-10">
        <h6><trans->سمة العرض</trans-></h6>
        <div class="flex gap-10 wrap">
          <button id="darkModeBtn" class="${getTheme()=="dark"?"bgc-primary":"bgc"} medium">
            <i- type="moon"></i->
            <p class="txt body1"><trans->الوضع المضلم</trans-></p>
          </button>
          <button id="lightModeBtn" class="${getTheme()=="light"?"bgc-primary":"bgc"} medium">
            <i- type="sun"></i->
            <p class="txt body1"><trans->الوضع الفاتح</trans-></p>
          </button>
        </div>
      </div>
    `;
    let langaugeSettings = `
      <div class="flex column gap-10">
        <h6><trans->اللغة</trans-></h6>
        <div class="flex gap-10 wrap">
          <button title="${trans('العربية')}" id="arabicBtn" class="${getLanguage()=="ar"?"bgc-primary":"bgc"} medium">
            <i- type="flag-KSA"></i->
            <p class="txt body1">العربية</p>
          </button>
          <button title="${trans('الإنجليزية')}" id="englishBtn" class="${getLanguage()=="en"?"bgc-primary":"bgc"} medium">
            <i- type="flag-US"></i->
            <p class="txt body1">English</p>
          </button>
        </div>
      </div>
    `;
    
    // Append container \\
    settingsElement.innerHTML += `
      <div class="container">
        <div class="settingsGroupContainer flex gap-10">
          <button id="display" class="bgc-primary medium">
            <p class="txt body1">${trans('العرض')}</p>
          </button>
          <button id="account" class="bgc medium">
            <p class="txt body1">${trans('الحساب')}</p>
          </button>
        </div>

        
        <section class="display flex column gap-20">
          ${themesSettings}
          ${langaugeSettings}
        </section>

        <section class="account hidden flex column gap-10 align-start">
          <div class="profile flex align-center gap-10">
            <img src="${profile_img}" alt="${trans('صورة الملف الشخصي')}"/>
            <div class="flex column" style="gap:2px">
              <h6 id="username"></h6>
              <p id="user_email" class="txt body1 txt-color-gray"></p>
            </div>
          </div>
          
          <button id="logout_btn" class="medium bgc">
            <p class="txt body1 txt-color-red">${trans('تسجيل الخروج')}</p>
          </button>
        </section>
        
      </div>
    `;
    
    let settingsGroupContainer = settingsElement.querySelector('.settingsGroupContainer');

    let displayGroupHandel = settingsGroupContainer.querySelector('button#display');
    let accountGroupHandel = settingsGroupContainer.querySelector('button#account');

    // Sections
    let displaySection = settingsElement.querySelector('section.display');
    let accountSection = settingsElement.querySelector('section.account');
    
    // === Show & Hide sections === \\
    displayGroupHandel.addEventListener('click', ()=>{

      // display section
      displaySection.classList.remove('hidden'); // Show section
      displayGroupHandel.classList.add('bgc-primary'); // Add primary color
      displayGroupHandel.classList.remove('bgc'); // Remove default color

      // account section
      accountSection.classList.add('hidden'); // Hide Section
      accountGroupHandel.classList.add('bgc'); // Add default color
      accountGroupHandel.classList.remove('bgc-primary'); // Remove primary color
    })
    accountGroupHandel.addEventListener('click', ()=>{

      // account section
      accountSection.classList.remove('hidden'); // Show Section
      accountGroupHandel.classList.add('bgc-primary'); // Add primary color
      accountGroupHandel.classList.remove('bgc'); // Remove default color

      // display section
      displaySection.classList.add('hidden'); // Hide section
      displayGroupHandel.classList.add('bgc'); // add default color
      displayGroupHandel.classList.remove('bgc-primary'); // Remove primary color
    })
    
    // === Change theme === \\
    let lightModeBtn = settingsElement.querySelector('#lightModeBtn');
    let darkModeBtn =  settingsElement.querySelector('#darkModeBtn');

    darkModeBtn.addEventListener('click', ()=>{
      switchTheme('dark');
      darkModeBtn.classList.add('bgc-primary');
      darkModeBtn.classList.remove('bgc');

      lightModeBtn.classList.remove('bgc-primary');
      lightModeBtn.classList.add('bgc');
    })
    lightModeBtn.addEventListener('click', ()=>{
      switchTheme('light');
      lightModeBtn.classList.add('bgc-primary');
      lightModeBtn.classList.remove('bgc');

      darkModeBtn.classList.remove('bgc-primary');
      darkModeBtn.classList.add('bgc');
    })

    // === Change langauge === \\
    let arabicBtn  = settingsElement.querySelector('#arabicBtn');
    let englishBtn = settingsElement.querySelector('#englishBtn');
    arabicBtn.addEventListener('click', ()=>{
      if(getLanguage() == 'ar') return;
      changeLanguage('ar');
      arabicBtn.classList.add('bgc-primary');
      arabicBtn.classList.remove('bgc');

      englishBtn.classList.remove('bgc-primary');
      englishBtn.classList.add('bgc');
    })
    englishBtn.addEventListener('click', ()=>{
      if(getLanguage() == 'en') return;
      changeLanguage('en');
      englishBtn.classList.add('bgc-primary');
      englishBtn.classList.remove('bgc');

      arabicBtn.classList.remove('bgc-primary');
      arabicBtn.classList.add('bgc');
    })

    // Append settings element into overlay element \\
    overlayElement.append(settingsElement);
    // append overlay element into body \\
    document.body.append(overlayElement);

    // === Account settings === \\
    let username_cont = settingsElement.querySelector('#username'); // Username container
    let email_cont = settingsElement.querySelector('#user_email'); // Email container

    const userData = getPayload(localStorage.getItem('JWT'));

    username_cont.innerText = userData.full_name;
    email_cont.innerText = userData.email;
    
    let logoutBtn = settingsElement.querySelector('button#logout_btn');
    logoutBtn.addEventListener('click', logout);
    
    // === Close window === \\
    function closeWindow() {
      overlayElement.remove();
      settingsBtn.classList.remove('bgc-primary'); // Add primary background color to settings toggle \\
    }
    // Click on overlay
    overlayElement.addEventListener('click', event => {
      if(event.target == overlayElement) closeWindow() ;
    })
    // Click on close Btn
    settingsElement.querySelector('#closeWindowBtn').addEventListener('click', ()=> closeWindow());    
    
  } 
  settingsBtn.addEventListener('click', settingsHandel);
  
  
})()}