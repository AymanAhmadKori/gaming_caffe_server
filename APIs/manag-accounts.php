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
    case '!exists: mode':
      $message("Undefined `mode`");
    break;

    // getAll-accounts
    case '!isset: limit':
      $message("Undefined `limit`: limit is important in `getAll-accounts` mode");
      break;
    case '!isset: except':
      $message("Undefined `except`: except is important in`getAll-accounts` mode");
    break;
    
    // search-account
    case '!isset: by in search-account mode':
      $message("Undefined `by`: by is important in`search-account` mode");
      break;
    case '!isset: id in search-account by id mode':
      $message("Undefined `id`: id is important in`search-account` by id mode");
      break;
    case '!isset: email in search-account by email mode':
      $message("Undefined `email`: email is important in`search-account` by email mode");
    break;
    
    
    //=== Invalid errors === \\
    case 'invalid: mode':
      $message("Invalid `mode`");
    break;
    
    // getAll-accounts
    case 'invalid: limit':
      $message("Invalid `limit`: Value moust be integer > 0");
    break;
    
    case 'invalid: except not-array':
      $message("Invalid `except`: type not array");
      break;
    case 'invalid: except value':
      $message("Invalid `except`: All values moust be type of integer");
    break;
    
    
    // search-account
    case 'invalid: by in search-account mode':
      $message("Invalid `by` value");
      break;
    case 'invalid: id in search-account by id mode': 
      $message('Invalid `id` value');
      break;
    case 'invalid: email in search-account by email mode': 
      $message('Invalid `email` value');
    break;
  }

}

// Request
$request = file_get_contents('php://input');

// Get request data
$data = json_decode($request, true);

// Request modes
define('request_modes',[
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
]);

// Search account modes
define('search_account_modes',[
  'id',
  'email'
]);

// Validate request data 
(function(){
  global $data;

  // Check if manag mode exists \\
  if(!isset($data['mode'])) error('!exists: mode');

  // Get manage mode
  $mode = $data['mode'];

  // Validation mode
  if(!in_array($mode, request_modes)) error('invalid: mode');

  switch ($mode) {
    case 'getAll-accounts':
      // Check limit validation
      if( !isset($data['limit']) ) error('!isset: limit');
        $limit = $data['limit'];

        // Check if limit valid integer
        if(!is_int(+$limit) || $limit <= 0) error('invalid: limit');
      //

      // Check except validation
      if( !isset($data['except']) ) error('!isset: except');
        $except = $data['except'];
        
        // Check if type of except is array
        if(!is_array($except)) error('invalid: except not-array');
      
        // Check except IDs
        $types = array_unique(array_map('gettype', $except));
        if(count($types) > 1 || $types[0] !== 'integer') error('invalid: except value');
      //
    break;

    case 'search-account':
      // Validate `by`
      if( !isset($data['by'])) error('!isset: by in search-account mode');
        $by = $data['by'];
        
        if(!in_array($by, search_account_modes)) error('invalid: by in search-account mode');
      //

      // Validate `id`
      if($by == 'id') {
        if( !isset($data['id']) ) error('!isset: id in search-account by id mode');
        $id = $data['id'];

        if(!is_int($id) || $id < 0) error('invalid: id in search-account by id mode');
        
      }

      // Validate `email`
      if($by == 'email') {
        if( !isset($data['email']) ) error('!isset: email in search-account by email mode');
        $email = $data['email'];

        if(!filter_var($email, FILTER_VALIDATE_EMAIL)) error('invalid: email in search-account by email mode');
        
      }
      
    break;


  }
  
})();
