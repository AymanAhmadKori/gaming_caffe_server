
// default theme 
const defaultTheme = 'dark'; // light | dark

// Theme css link 
let themeLink = incCSS();
themeLink.rel = 'stylesheet';

let darkmodeFilePath   = themesDir + "darkmode.css";
let lightmodeFilePath  = themesDir + "lightmode.css";

if(!localStorage.getItem('theme')) {
  localStorage.setItem('theme',defaultTheme);
  switchTheme(defaultTheme);
}

function switchTheme(theme = '') {

  function lightmode() {
    themeLink.href = lightmodeFilePath;
    localStorage.setItem('theme','light');
  };

  function darkmode() {
    themeLink.href = '';
    localStorage.setItem('theme','dark');
    document.body.removeAttribute('data-theme');
  };

  if(theme == 'light') {lightmode(); return true}
  if(theme == 'dark') {darkmode(); return true}

  if(localStorage.getItem('theme') == 'light') {darkmode(); return 'switch'}
  lightmode();
  return 'switch';
}

function getTheme() {
  if(!localStorage.getItem('theme')) {return defaultTheme}
  return localStorage.getItem('theme');
}

switchTheme(localStorage.getItem('theme'));

document.head.append(themeLink);