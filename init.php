<?php


// Define JWT keys
define("PRIVATE_KEY", file_get_contents("includes/JWT-keys/private.key"));
define("PUBLIC_KEY", file_get_contents("includes/JWT-keys/public.key"));

// Paths
define("jwtFile", "includes/jwt.php");