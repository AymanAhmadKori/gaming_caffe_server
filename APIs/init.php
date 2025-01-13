<?php 

// if($_SERVER['REQUEST_METHOD'] != 'POST') {
//   header('location: ../index.html');
//   exit();
// }

// paths //
define("jconn", "connect.php");

// Define JWT keys
define("PRIVATE_KEY", file_get_contents("../includes/JWT-keys/private.key"));
define("PUBLIC_KEY", file_get_contents("../includes/JWT-keys/public.key"));

// Paths
define("jwtFile", "../includes/jwt.php");