// ==== Paths ==== //
const layoutDir = 'layout/';
const incDir = 'includes/';

// Includes
const jsDir = layoutDir + "js/";
const cssDir = layoutDir + "css/";

// Includes
const themesDir = incDir + "themes/";
const tempDir = incDir + "templates/";
const langDir = incDir + 'languages/';

// Profile image
const profile_img = layoutDir + 'imgs/profile.svg';

// === Favicon === \\
const Favicon = document.createElement('link');
Favicon.type = 'image/x-icon';
Favicon.href = layoutDir + "imgs/favicon.svg";
Favicon.rel = 'shortcut icon';
document.head.appendChild(Favicon);

// === Manifest file === \\
let manifestLink = document.createElement('link');
manifestLink.rel = "manifest";
manifestLink.href = "manifest.json";
document.head.append(manifestLink);


/**
 * Dynamically loads a JavaScript file from a specified directory.
 * @param {string} path_in_jsDir - The relative path to the JavaScript file within the `jsDir` directory.
 * @returns {HTMLScriptElement|null} - The created script element, or null if the input is invalid.
 */
function incJS(path_in_jsDir) {
  // Validate the file extension to ensure it is a JavaScript file
  if (path_in_jsDir.split('.').pop().toLowerCase() !== 'js') {
    console.error('Please enter a valid path to a JavaScript file');
    return null; // Exit if the file is not a JavaScript file
  }

  // Create a new <script> element to load the JavaScript file
  let script = document.createElement('script');
  script.src = jsDir + path_in_jsDir; // Set the source of the script to the provided path

  // Append the script element to the document body to load and execute it
  document.body.append(script);

  // Return the created script element for further use if needed
  return script;
}

// Include index file \\
incJS('index.js');

/**
 * Dynamically loads a CSS file from a specified directory or returns a blank link element.
 * @param {string} path_in_cssDir - The relative path to the CSS file within the `cssDir` directory. Defaults to an empty string.
 * @returns {HTMLLinkElement|null} - The created link element, or null if the input is invalid.
 */
function incCSS(path_in_cssDir = '') {
  // If the path is not empty, validate the file extension to ensure it is a CSS file
  if (path_in_cssDir && path_in_cssDir.split('.').pop().toLowerCase() !== 'css') {
    console.error('Please enter a valid path to a CSS file');
    return null; // Exit if the file is not a CSS file
  }

  // Create a new <link> element for the CSS file
  let link = document.createElement('link');
  link.rel = 'stylesheet'; // Set the relation to "stylesheet"

  // If no path is provided, return the blank link element
  if (!path_in_cssDir) return link;

  // Otherwise, set the href attribute to the full path of the CSS file
  link.href = cssDir + path_in_cssDir;

  // Append the link element to the <head> of the document to load the styles
  document.head.append(link);

  // Return the created link element for further use if needed
  return link;
}

/**
 * Fetches a file from the Includes directory (incDir) if it has a supported extension (HTML or JSON).
 * If successful, returns the file content as text. If there's an error, it returns an error message.
 * 
 * @param {string} path_in_incDir - The path of the file inside the tempDir.
 * 
 * @returns {Promise<string>} A promise that resolves with the file content if successful,
 *                            or an error message if an issue occurs.
 */
function fetchFile(path_in_incDir) {
  return new Promise((resolve, reject) => {
    const supportedFetchFileExtensions = ['HTML', 'JSON']; // List of supported file extensions

    // Extract the file extension from the path and convert it to uppercase
    let ext = path_in_incDir.split('.').pop().toUpperCase();

    // Check if the file extension is supported
    if (!supportedFetchFileExtensions.includes(ext)) {
      return reject(
        "You can only fetch " + 
        supportedFetchFileExtensions.join(' & ') + 
        " files" // Reject with an error if the file extension is not supported
      );
    }

    // Attempt to fetch the file from the Includes directory
    fetch(incDir + path_in_incDir)
      .then(res => {
        // Check if the HTTP response status is not successful
        if (!res.ok) {
          // Throw an error with the status code and status text
          throw new Error(`Failed to fetch: ${res.status} ${res.statusText}`);
        }
        return res.text(); // Parse and return the response text
      })
      .then(data => resolve(data)) // Resolve the promise with the fetched data
      .catch(err => reject(err)); // Reject the promise with an error if something fails
  });
}

// Real time alert \\
const alertsContainer = document.createElement('div');
alertsContainer.id = 'Real_Time_alerts_container';
document.body.append(alertsContainer);

function pushRealTimeAlert(status, message) {
  // Alert Statuses
  const statuses = ['warning', 'error', 'success'];

  // Check them status
  if(!statuses.includes(status)) {
    console.error('invalid status "' + status + '"');
    return false
  }

  // Append alerts container if not exsists
  if(!document.getElementById('Real_Time_alerts_container')) {document.body.append(alertsContainer)}
  
  // Create alert element
  let alert = document.createElement('div');
  alert.classList.add('alert', status);

  // Set alert message
  alert.innerHTML += `<div class="message">${message}</div>`;
  
  // Insert alert into alerts container 
  alertsContainer.append(alert);
  setTimeout(()=>alert.classList.add('show'), 0); // To active the animation

  
  // Get transition to remove alert after end of animation
  const alertStyles = getComputedStyle(alert);
  let transition = parseFloat(alertStyles.transition.slice(0, -1)) * 1000; // To remove alert after the animation end's

  // Remove alert handel
  function removeAlert() {
    alert.classList.remove('show');
    setTimeout(() => {
      alert.remove();
    }, transition);
  }
  
  // Delay before remove the alert
  let removeTimeoutHandle = setTimeout(() => removeAlert(), 3000);
    
  // Cansel the delay
  alert.onclick = ()=> {
    clearTimeout(removeTimeoutHandle);
    removeAlert();
  };

}

/* حل مشكلة ظهور فلاش باللون الأسود في الوظع الفاتح عند تحميل الصفحة*/
switch (localStorage.getItem('theme')) {
  case 'light':
    document.body.setAttribute('data-theme','light');
  break;
}

// == Language == \\
const DefaultLanguage = 'ar'; // Default language ** You cannot changed ! **
const supportedLanguages = ['ar', 'en'];

let systemLanguage = localStorage.getItem('language');
if (
  !systemLanguage
  ||
  !supportedLanguages.includes(systemLanguage) // check if the language are supported
) {
  localStorage.setItem('language', DefaultLanguage);
  systemLanguage = DefaultLanguage;
}

// Page direction \\
let pageDirection = 'ltr';

// set direction //
if(systemLanguage == 'ar') {
  document.body.setAttribute('data-direction-rtl','');
  pageDirection = 'rtl';
}


const lang = {
  "en" : {
    // معلومات عامة \\
    "هاتف": "tel",
    "مجانا": "Free",
    "المتابعة بـ": "Continue with",
    "جوجل": "Google",
    "صورة الملف الشخصي": "Profile picture",

    // الإعدادات \\
    "النظام": "System",
    "العرض": "Display",
    "الحساب": "Account",
    "سمة العرض": "Theme",
    "اللغة": "Language",
    "العربية": "Arabic",
    "الإنجليزية": "English",
    "الوضع المضلم": "Dark mode",
    "الوضع الفاتح": "Light mode",

    // أزرار عامة \\
    "حفظ": "Save",
    "إلغاء": "Cancel",
    "حذف": "Delete",
    "بحث": "Search",
    "الاسم": "Name",
    "الكل": "All",
    "التاريخ": "Date",
    "لا": "No",
    "نعم": "Yes",
    "من": "From",
    "الى": "To",
    "نشط": "Active",
    "غير نشط": "Inactive",
    "دقيقة": "min",
    "تنزيل": "Download",
    "تسجيل الخروج": "Logout",
    "اتصل بنا": "Contact us",

    // عناوين \\
    "تم حظر هذا الحساب.": "This account has been banned.",
    
    // العملات \\
    "د.ل": "LYD",

    // الصفحات \\
    "الرئيسية": "Home",
    "إدارة الحسابات": "Manage Accounts",
    "سجل الإشتراكات": "Subscription history",
    "الإعدادات": "Settings",
    "تسجيل الدخول": "Login",
    "حساب محظور": "Banned account",

    // رسائل التنبيه \\
    // نجاح    
    // أخطاء    
    // تحذير
    // فشل
    "حدث خطأ. حاول مرة أخرى.": "An error occurred. Try again.",

    // رموز خاصة \\
    "؟": "?",
    "\\": "/"
  }
};

function trans(word, transElement = undefined) {

  if(!lang[systemLanguage]) {
    if(transElement) {transElement.outerHTML = word; return}
    return word;
  }
    
  function undefined_word() {
    
    console.error(

      'undefined word "' 
      + word
      + '" in '
      + systemLanguage
      + ' dictionary.',
      'element',
      transElement
    );
    return word;
  }

  // Replace trans- element if the word exsists
  if(transElement && lang[systemLanguage][word]) {
    transElement.outerHTML = lang[systemLanguage][word];
  }

  if(lang[systemLanguage][word]) return lang[systemLanguage][word];
  return undefined_word();
}
function changeLanguage(language) {
  if(!supportedLanguages.includes(language)) return null;

  localStorage.setItem('language', language);
  location.reload();
}
function getLanguage() {
  if(!localStorage.getItem('language')) {return DefaultLanguage}
  return localStorage.getItem('language');
}

// === Create custom Translate element === \\
class TransElement extends HTMLElement {
  connectedCallback() {
    trans(this.innerText, this);
  }
}
// Element name "<trans-></trans->"
customElements.define("trans-", TransElement);

// Translate Page Title 
const pageTitle = document.title;
document.title = trans(document.title);

// === Functions === \\
// Debounce
function debounce(fn, wait) {
  let timeout;
  return function (...args) {
    clearTimeout(timeout);
    timeout = setTimeout(()=>fn(...args), wait)
  }
}

// Get curretn UTC
function getCurrentUTC() {
  return new Date().toISOString();
}

// Round cents
function roundCents(number) {
  const integerPart = Math.floor(number);
  const fractionalPart = number - integerPart;

  let roundedFractionalPart;

  if (fractionalPart < 0.125) {
    roundedFractionalPart = 0.0;
  } else if (fractionalPart < 0.375) {
    roundedFractionalPart = 0.25;
  } else if (fractionalPart < 0.625) {
    roundedFractionalPart = 0.50;
  } else if (fractionalPart < 0.875) {
    roundedFractionalPart = 0.75;
  } else {
    roundedFractionalPart = 1.0;
  }

  const roundedNumber = integerPart + roundedFractionalPart;
  return roundedNumber;
}

// 00:00:00
function formatTime(seconds) {
  const hours = Math.floor(seconds / 3600);
  const minutes = Math.floor((seconds % 3600) / 60);
  const remainingSeconds = Math.floor(seconds % 60);

  const formattedHours = String(hours).padStart(2, '0');
  const formattedMinutes = String(minutes).padStart(2, '0');
  const formattedSeconds = String(remainingSeconds).padStart(2, '0');

  if (hours > 0) {
    return `${formattedHours}:${formattedMinutes}:${formattedSeconds}`;
  }
  return `${formattedMinutes}:${formattedSeconds}`;
}

// Get timezone
function getTimezone() {
  return Intl.DateTimeFormat().resolvedOptions().timeZone
}

// Format readable date
function formatReadableDate(utcString, timeZone = getTimezone()) {
  let date = new Date(utcString);

  let options = {
      day: "numeric",
      // month: "long",
      month: "numeric",
      year: "numeric",
      hour: "2-digit",
      minute: "2-digit",
      // second: "2-digit",
      hour12: true,
      timeZone: timeZone
  };

  return date.toLocaleString(getLanguage(), options);
}

function goToPage(url) {
  window.location.href = url;
}
function logout() {
  localStorage.removeItem('JWT');
  goToPage('login.html');
}

// === JWT functions === \\
/** Verifies the JWT signature.
*
* This function checks the authenticity of a JWT by verifying its signature
* against the data encoded in the header and payload, using the provided public key.
*
* @param {string} jwt The JWT token to verify.
* @param {CryptoKey} publicKey The public key as a CryptoKey object.
* @returns {Promise<boolean>} True if the signature is valid, False otherwise.
*/
async function verifyJWT(jwt) {
  let publicKey = `
    MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAs4/HicRv9+SnA7ls+IEr
    mELmhKmj70PXcNQp535rhjybVuczfQ0rbD1rdiT6P0zPS2KAt0F6haYNuAOdh71S
    8eYMyxFflg5W3lqPgESubIkfaLTcb3vjoL3YECO6+IV6t14fxK2hPE12Elm3Uz35
    Lf0sl+Mzu7HDuJZr7x1tn8SDEz3kh8R0Fz7V0Fith1mz/nqU4dyVtO8lHLZco0TU
    GdLokGQ+rOPtN5V0OhFyDjUv/kQhqTHdNl4RN8iuE3pKxjCdRhm4VxUl6qe1iyxK
    Ny8mVcG5ehgz9QlD0YYZBQTCH2A0O7P3Xp98CfYqkfgWqyY1rXk09PuQmEtZZ6ax
    pwIDAQAB
  `;
  if(!jwt) return false;
  
  // Split the JWT into its three parts: header, payload, and signature
  const parts = jwt.split('.');

  // If the JWT does not have exactly 3 parts, it is malformed
  if (parts.length !== 3) {
    return false; // Invalid format
  }

  // Assign the parts to variables
  const [headerEncoded, payloadEncoded, signatureEncoded] = parts;

  // Recreate the data to verify (header + payload)
  const dataToVerify = new TextEncoder().encode(`${headerEncoded}.${payloadEncoded}`);

  // Decode the signature from Base64 URL-safe format
  const signature = base64UrlToUint8Array(signatureEncoded);

  // Verify the signature using the public key and SHA-256 algorithm
  return crypto.subtle.verify(
    { name: "RSASSA-PKCS1-v1_5", hash: "SHA-256" },
    await importPublicKey(publicKey),
    signature,
    dataToVerify
  );
}

/** Extracts and decodes the payload from a JWT token.
*
* @param {string} jwt The JWT token.
* @returns {Object|null} The decoded payload, or null if invalid.
*/
function getPayload(jwt) {
  if(!jwt) return null;

  const parts = jwt.split('.');
  if (parts.length !== 3) {
    return null; // Invalid format
  }

  try {
    const payloadDecoded = atob(base64UrlToBase64(parts[1]));
    return JSON.parse(JSON.parse(payloadDecoded));
  } catch (e) {
    return null; // Invalid JSON
  }
}

/** Converts Base64 URL-encoded string to standard Base64.
* @param {string} input The Base64 URL-encoded string.
* @returns {string} The standard Base64 string.
*/
function base64UrlToBase64(input) {
return input.replace(/-/g, '+').replace(/_/g, '/');
}

/** Decodes a Base64 URL-encoded string to a Uint8Array.
* @param {string} input The Base64 URL-encoded string.
* @returns {Uint8Array} The decoded Uint8Array.
*/
function base64UrlToUint8Array(input) {
const base64 = base64UrlToBase64(input);
const binaryString = atob(base64);
const len = binaryString.length;
const bytes = new Uint8Array(len);
for (let i = 0; i < len; i++) {
    bytes[i] = binaryString.charCodeAt(i);
}
return bytes;
}

/** Converts a2 PEM public key string to a CryptoKey object.
* @param {string} pem The public key in PEM format.
* @returns {Promise<CryptoKey>} The CryptoKey object.
*/
async function importPublicKey(pem) {
// إزالة هيدر وتذييل PEM
const base64Key = pem.replace(/\s/g, ""); // إزالة المسافات والأسطر الجديدة

// تحويل Base64 إلى Uint8Array
const binaryKey = Uint8Array.from(atob(base64Key), c => c.charCodeAt(0));

// استيراد المفتاح كـ CryptoKey
return crypto.subtle.importKey(
    "spki", // صيغة المفتاح العام
    binaryKey,
    { name: "RSASSA-PKCS1-v1_5", hash: "SHA-256" },
    true, 
    ["verify"] // يُستخدم فقط للتحقق
);
}

// === Includes === //
// Components
incCSS('compos.css');
incJS('compo.js');

// Themes
incJS('theme.js');

// Include nav bar
if(document.querySelector('nav#navContainer')){
  incCSS('navBar.css');
  incJS('navBar.js')
}

// Check JWT token
(async ()=>{
  // Cancel in login page
  if (pageTitle == "تسجيل الدخول") return;
  
  function inValidJWT() {
    // Delete JWT from localstorage
    localStorage.removeItem('JWT');
    
    // Go to login page
    goToPage('login.html')
  }
  
  const JWT = localStorage.getItem('JWT');

  // Validate JWT
  if( !(await verifyJWT(JWT)) ) inValidJWT();

  // Get payload
  const payload = getPayload(JWT);
  
  // Check JWT expiry
  let expiry = new Date(payload.expiry).getTime();
  let now = new Date().getTime();

  if(expiry <= now) inValidJWT();
  
  // Check group_id
  if( !(payload.admin) ) inValidJWT();
})();

// Include login page files \\
if(pageTitle == "تسجيل الدخول"){
  incJS('login.js');
}

// Database \\
incJS('dexie.min.js').onload = ()=>{
  incJS('database.js')
}

// after loaded includes
addEventListener('load',()=>{
})

// ==== Service worker ==== \\
if ('serviceWorker' in navigator) {
  navigator.serviceWorker.register('sw.js');
}