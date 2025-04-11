// Set search_by [buttons] to place in small screens
(()=>{
  // Screen width
  const screenWidth = 530;

  // Get elements
  let mainBar = document.querySelector('.mainBar');
  let search_section = mainBar.querySelector('section.search');
  let search_btns = mainBar.querySelector('.search_buttons');

  // Handel
  function handel() {
    if(window.innerWidth <= screenWidth) {
      // Small screens place
      mainBar.parentElement.insertBefore(search_btns, mainBar);
    } else {
      search_section.append(search_btns);
    }
  }handel()
  
  // Set resize event
  addEventListener('resize', handel);
})();