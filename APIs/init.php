<?php 

if($_SERVER['REQUEST_METHOD'] != 'POST') {
  http_response_code(404);
  exit();
}

// Array of allowed domains
$allowed_origins = [
  "http://localhost",
];
// Check if the request's origin is in the allowed origins array
if (!in_array($_SERVER['HTTP_ORIGIN'], $allowed_origins)) {
  http_response_code(404);
  exit();
}


// Define JWT keys
define("PRIVATE_KEY", file_get_contents("../includes/JWT-keys/private.key"));
define("PUBLIC_KEY", file_get_contents("../includes/JWT-keys/public.key"));

// Paths
define("jwtFile", "../includes/jwt.php");
define('funcs', '../includes/functions/');

// Use in connect 
define('INIT', true);

// === Includes === \\
// JWT file
include jwtFile;

// Functions
include funcs . 'funcs.php';