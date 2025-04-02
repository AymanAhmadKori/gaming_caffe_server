<?php 
include 'init.php';

/* Documentation
  Modes : ( ** = require,  T = type )
  - getAll-accounts : returns array
  - - **`limit` T int
  - - **`except` T array

  - search-account : returns object || null
  - - **`by` T string [ email - id ]
  - - ** `email` T string || ** `id` T int

  - ban-account & unBlock-account : returns bool
  - - ** `id` T int

  - set-sub : returns bool
  - - ** `id` T int
  - - ** `plan-id` T int
  - - ** `plan-cycles` T int

  - get-sub: returns object || null
  - - ** `account-id` T int
  
  - cancel-sub: returns bool
  - - ** `account-id` T int
  - - ** `sub-id` T int

  - getAll-subs-history: returns array
  - - ** `limit` 
  - - ** `except` 
  
*/

// Error response
function error(string $type) {
  $message = function (string $message) {
    echo json_encode(["error" => $message]);
    exit();
  };

  switch($type) {
    // === Undefined errors === \\
    case '!exists_mode': $message("Undefined `mode`"); break;
    case '!isset_limit':   $message("Undefined `limit`: limit is important in `getAll_account` mode"); break;
    case '!isset_except':  $message("Undefined `except`: except is important in`getAll_account` mode"); break;

    //=== Invalid errors === \\
    case 'invalid_mode': $message("Invalid `mode`"); break;
    case 'invalid_limit': $message("Invalid `limit`: Value moust be integer > 0"); break;
    
    case 'invalid_except: not-array': $message("Invalid `except`: type not array"); break;
    case 'invalid_except: value': $message("Invalid `except`: All values moust be type of integer"); break;
  }

}

// Request
$request = file_get_contents('php://input');

// Get request data
$data = json_decode($request, true);

// Request modes
$request_modes = [
  // Account
  'getAll-accounts',
  'search-account',
  'ban-account',
  'unBlock-account',

  // Subscription
  'set-sub',
  'get-sub',
  'cancel-sub',
  'getAll-subs-history'
];

// Validate request data 
(function(){
  global $data;
  global $request_modes;

  // Check if manag mode exists \\
  if(!isset($data['mode'])) error('!exists_mode');

  // Get manage mode
  $mode = $data['mode'];

  // Validation mode
  if(!in_array($mode, $request_modes)) error('invalid_mode');
  
})();
