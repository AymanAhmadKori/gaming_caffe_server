<?php 
include 'init.php';

/* Documentation
  Modes : ( ** = require,  T = type )
  - getAll-accounts : returns array
  - - **`limit` T int
  - - **`except` T array

  - search-accounts : returns array of object
  - - **`by` T string [ email - id ]
  - - ** `email` T string || ** `id` T int || `full_name` T string

  - ban-account : returns bool
  - - ** `account_id` T int
  - - ** `ban_type` T int
  - - ** `Unblock_at` T string [ISO time]

  - unBlock-account : returns bool
  - - ** `account_id` T int

  - set-sub : returns bool
  - - ** `account-id` T int
  - - ** `plan-id` T int
  - - ** `cost` T float
  - - ** `expiry` T string [ISO time]

  - get-sub: returns object || null
  - - ** `account-id` T int
  
  - cancel-sub: returns bool
  - - ** `account-id` T int

  - getAll-subs-history: returns array
  - - ** `limit`
  - - ** `except`
  - - `of-account` T int
  
  - getAll-plans: returns array
  
  - update-plan: returns bool
  - - ** `plan_id` T int
  - - `cycle_duration` T int
  - - `price_per_cycle` T int|float
  - - `name` T string
*/

// Error response
function error(string $type) {
  $message = function (string $message) {
    echo json_encode(["error" => $message]);
    exit();
  };

  switch($type) {
    case 'default':
      $message('Theres an Error');
    break;
    
    // === Undefined errors === \\
      case '!exists: mode':
        $message("Undefined `mode`");
      break;

      ## getAll-accounts
        case '!isset: limit':
          $message("Undefined `limit`: limit is require in `getAll-accounts` mode");
          break;
        case '!isset: except':
          $message("Undefined `except`: except is require in `getAll-accounts` mode");
        break;
      #
      ## search-account
        case '!isset: by in search-account mode':
          $message("Undefined `by`: by is require in `search-account` mode");
          break;
        case '!isset: id in search-account by id mode':
          $message("Undefined `id`: id is require in `search-account` by id mode");
          break;
        case '!isset: email in search-account by email mode':
          $message("Undefined `email`: email is require in `search-account` by email mode");
          break;
        case '!isset: full_name in search-account by full_name mode':
          $message("Undefined `full_name`: full_name is require in `search-account` by full_name mode");
        break;
      #

      ## ban-account & unBlock-account
        case '!isset: account_id in ban-account || unBlock-account mode':
          $message('Undefined `account_id`: account_id is require in `ban-account` or `unBlock-account mode');
          break;
        case '!isset: ban_type in ban-account mode':
          $message('Undefined `ban_type`: ban_type is require in ban-account mode');
          break;
        case '!isset: unBlock_at in ban-account mode':
          $message('Undefined `unBlock_at`: unBlock_at is require in ban-account mode');
        break;
      #

      ## set-sub
        case '!isset: account_id in set-sub mode':
          $message('Undefined `account_id` : account_id is require in `set-sub` mode');
          break;
        case '!isset: plan_id in set-sub mode':
          $message('Undefined `plan_id` : plan_id is require in `set-sub` mode');
          break;
        case '!isset: expiry in set-sub mode':
          $message('Undefined `expiry` : expiry is require in `set-sub` mode');
          break;
        case '!isset: cost in set-sub mode':
          $message('Undefined `cost` : cost is require in `set-sub` mode');
        break;
      #
      
      ## get-sub & cancel-sub
        case '!isset: account_id in get-sub || cancel-sub mode':
          $message('Undefined `account_id` : account_id is require in `get-sub` or `cancel-sub` mode');
        break;
      #
      
      ## getAll-subs-history
        case "!isset: limit in getAll-subs-history mode":
          $message('Undefined `limit` : limit is require in `getAll-subs-history` mode');
          break;
        case "!isset: except in getAll-subs-history mode":
          $message('Undefined `except` : except is require in `getAll-subs-history` mode');
          break;
      #

      ## update-plan
        case '!isset: plan_id in update-plan mode':
          $message('Undefined `plan_id` : plan_id is require in `update-plan` mode');
        break;
        case '!isset: plan data':
          $message('Undefined `plan data` : Theres no data to update');
        break;
      #
      
    //
    
    //=== Invalid errors === \\
      case 'invalid: mode':
        $message("Invalid `mode`");
      break;
      
      ## getAll-accounts
        case 'invalid: limit':
          $message("Invalid `limit`: Value moust be integer > 0");
        break;
        
        case 'invalid: except not-array':
          $message("Invalid `except`: type not array");
          break;
        case 'invalid: except value':
          $message("Invalid `except`: All values moust be type of integer");
        break;
      #
      
      ## search-account
        case 'invalid: by in search-account mode':
          $message("Invalid `by` value");
          break;
        case 'invalid: id in search-account by id mode': 
          $message('Invalid `id` value');
          break;
        case 'invalid: email in search-account by email mode': 
          $message('Invalid `email` value');
          break;
        case 'invalid: full_name in search-account by full_name mode':
          $message('Invalid `full_name` value');
        break;
      #

      ## ban-account || unblock-account mode
        case 'invalid: account_id in ban-account || unblock-account mode':
          $message('Invalid `account_id` value');
          break;
        case 'invalid: ban_type in ban-account mode':
          $message('Invalid `ban_type` value');
          break;
        case 'invalid: unBlock_at in ban-account mode':
          $message('Invalid `unBlock_at` value not-ISO8601');
        break;
      #

      ## set-sub
        case 'invalid: account_id in set-sub mode':
          $message('Invalid `account_id` value');
          break;
        case 'invalid: plan_id in set-sub mode':
          $message('Invalid `plan_id` value');
          break;
        case 'invalid: expiry in set-sub mode':
          $message('Invalid `expiry` value not-ISO8601 or [ expiry <= now ]');
          break;
        case 'invalid: cost in set-sub mode':
          $message('Invalid `cost` value');
        break;
      #
      
      ## get-sub & cancel-sub
        case 'invalid: account_id in get-sub || cancel-sub mode':
          $message('Invalid `account_id` value');
        break;
      #

      ## getAll-subs-history
        case 'invalid: limit in getAll-subs-history mode':
          $message('Invalid `limit` value');
          break;
        case 'invalid: except in getAll-subs-history mode':
          $message('Invalid `except` value');
          break;
        case 'invalid: except in getAll-subs-history mode [not-array]':
          $message('Invalid `except` type not-array');
          break;
        case 'invalid: of_account in getAll-subs-history mode':
          $message('Invalid `of_account` value');
        break;
      #
      
      ## update-plan
        case 'invalid: plan_id in update-plan mode':
          $message('Invalid `plan_id` value');
        break;
        case 'invalid: cycle_duration in update-plan mode':
          $message('Invalid `cycle_duration` value');
        break;
        case 'invalid: price_per_cycle in update-plan mode':
          $message('Invalid `price_per_cycle` value');
        break;
        case 'invalid: name in update-plan mode':
          $message('Invalid `name` value');
        break;
      #
    //

    //=== Executing errors === \\ 
      case 'execute':
        $message('Error while executing statment');
      break;
      case 'execute: delete expired-sub in set-sub mode':
        $message('Error while deleting expired sub');
      break;
    //

  }

}

// Request
$request = file_get_contents('php://input');

// Get request data
$data = json_decode($request, true);

// Request modes
define('request_modes',[
  # Account
  'getAll-accounts',
  'search-accounts',
  'ban-account',
  'unBlock-account',

  # Subscription
  'set-sub',
  'get-sub',
  'cancel-sub',
  'getAll-subs-history',

  # Plans
  'getAll-plans',
  'update-plan'
]);

// Search account modes
define('search_account_modes',[
  'id',
  'full_name',
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
        if( count($types) !== 0 && (count($types) !== 1 || $types[0] !== 'integer') ) error('invalid: except value');
      //
    break;

    case 'search-accounts':
      ## Validate `by`
      if( !isset($data['by'])) error('!isset: by in search-account mode');
        $by = $data['by'];
        
        if(!in_array($by, search_account_modes)) error('invalid: by in search-account mode');
      #

      switch ($by) {
        ## Validate `id`
        case 'id':
          if( !isset($data['id']) ) error('!isset: id in search-account by id mode');
          $id = $data['id'];
  
          if(!is_int($id) || $id < 0) error('invalid: id in search-account by id mode');
        break;

        ## Validate `email`
        case 'email':
          if( !isset($data['email']) ) error('!isset: email in search-account by email mode');
          $email = $data['email'];
        break;
        
        ## Validate full_name
        case 'full_name':
          if( !isset($data['full_name']) ) error('!isset: full_name in search-account by full_name mode');
          $full_name = $data['full_name'];

          if( !validate_name($full_name) ) error('invalid: full_name in search-account by full_name mode');
        break;
      }

    break;

    case 'ban-account':
    case 'unBlock-account':
      // Validate `id`
      if(!isset($data['account_id'])) error('!isset: account_id in ban-account || unBlock-account mode');
      $account_id = $data['account_id'];
      
      if( !is_int($account_id) || $account_id < 0) error('invalid: account_id in ban-account || unblock-account mode');

      // Stop validating if on unBlock-account mode
      if($mode == 'unBlock-account') return;

      // Validate `ban_type`
      if( !isset($data['ban_type']) ) error('!isset: ban_type in ban-account mode');
        $ban_type = $data['ban_type'];

        if( !is_int($ban_type) || $ban_type < 0) error('invalid: ban_type in ban-account mode');
      //

      // Validate `unBlock_at`
      if( !isset($data['unBlock_at']) ) error('!isset: unBlock_at in ban-account mode');
        $unBlock_at = $data['unBlock_at'];

        if(empty($unBlock_at) || !isValidISO8601($unBlock_at)) error('invalid: unBlock_at in ban-account mode');
      //
    break;

    case 'set-sub':
      // Validate account_id
      if( !isset($data['account_id']) ) error('!isset: account_id in set-sub mode');
        $account_id = $data['account_id'];

        if( !is_int($account_id) || $account_id < 0) error('invalid: account_id in set-sub mode');
      //

      // Validate plan_id
      if( !isset($data['plan_id']) ) error('!isset: plan_id in set-sub mode');
        $plan_id = $data['plan_id'];

        if( !is_int($plan_id) || $plan_id < 0 ) error('invalid: plan_id in set-sub mode');
      //
      
      // Validate expiry
      if( !isset($data['expiry']) ) error('!isset: expiry in set-sub mode');
        $expiry = $data['expiry'];
        
        if( !isValidISO8601($expiry) ) error('invalid: expiry in set-sub mode');

        $expiry = date->iso_to_mil($expiry);
        $now = date->now_mil();

        if(($expiry - $now) <= 0) error('invalid: expiry in set-sub mode');
      //

      // Validate cost
      if( !isset($data['cost']) ) error('!isset: cost in set-sub mode');
        $cost = $data['cost'];
        
        if($cost === '' || !in_array(gettype($cost), ['integer', 'double']) || $cost < 0) error('invalid: cost in set-sub mode');
      //
    break;

    case 'get-sub':
    case 'cancel-sub':
      // Validate account-id
      if( !isset($data['account_id']) ) error('!isset: account_id in get-sub || cancel-sub mode');
        $account_id = $data['account_id'];
        
        if( !is_int($account_id) || $account_id < 0) error('invalid: account_id in get-sub || cancel-sub mode');
      //
    break;

    case 'getAll-subs-history':
      // Validate limit
      if( !isset($data['limit']) ) error('!isset: limit in getAll-subs-history mode');
        $limit = $data['limit'];

        if( !is_int($limit) || $limit <= 0) error('invalid: limit in getAll-subs-history mode');
      //
        
      // Validate except
      if( !isset($data['except']) ) error('!isset: except in getAll-subs-history mode');
        $except = $data['except'];

        if( !is_array($except) ) error('invalid: except in getAll-subs-history mode [not-array]');
        
        // Check types
        $types = array_unique(array_map('gettype', $except));

        if(count($types) > 0 && (count($types) > 1 || $types[0] !== 'integer')) error('invalid: except in getAll-subs-history mode');        
      //

      // Validate of_account if exists
      if(isset($data['of_account'])) {
        $of_account = $data['of_account'];
        
        if( !is_int($of_account) || $of_account < 0) error('invalid: of_account in getAll-subs-history mode');
      }
    break;
    
    case 'update-plan':

      ## Validate plan_id
      if( !isset($data['plan_id']) ) error('!isset: plan_id in update-plan mode');
        $plan_id = $data['plan_id'];
        
        if( !is_int($plan_id) || $plan_id < 0) error('invalid: plan_id in update-plan mode');
      #

      ## Vlidate cycle_duration
      if( isset($data['cycle_duration']) ) {
        $cycle_duration = $data['cycle_duration'];

        if( !is_int($cycle_duration) || $cycle_duration <= 0) error('invalid: cycle_duration in update-plan mode');
      }

      ## Validate price_per_cycle
      if( isset($data['price_per_cycle']) ) {
        $price_per_cycle = $data['price_per_cycle'];

        if( 
          !(is_int($price_per_cycle) || is_float($price_per_cycle))
          || $price_per_cycle < 0 
        ) error('invalid: price_per_cycle in update-plan mode');
      }

      ## Validate name
      if( isset($data['name']) ) {
        $name = trim($data['name']);

        if(!validate_name($name) ) error('invalid: name in update-plan mode');
      }

      if(
        !isset($data['cycle_duration']) &&
        !isset($data['price_per_cycle']) &&
        !isset($data['name'])
      ) error('!isset: plan data');
      
    break;
  }
  
})();

// Get request mode
$request_mode = $data['mode'];

// Response with
function response_with($data) {
  // Show response
  echo json_encode([
    "data" => $data
  ]);

  // Exit;
  exit();
}

switch ($request_mode) {
  // Returns: Array of objects
  case 'getAll-accounts':
    // Limit & Excepts
    $limit = $data['limit'];
    $except = array_unique($data['except']);
    
    // Query
    $q = "SELECT `id`, `full_name`, `email`, `entry_date` FROM `accounts` ";
    $params = [...$except];

    // Add except in query
    if( count($except) > 0 ) {
      $q .= 'WHERE `id` NOT IN('. implode(',', array_map(fn()=> '?', $except)) .') '; // (?,?,?)
    }

    // Add LIMIT in query
    $q .= "LIMIT " . $limit;
    
    // Connect to database
    include connectFile;

    // Create statment & executed
    $stmt = $pdo->prepare($q);
    $stmt->execute($params);

    // Get response from statment
    $response = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Close database connection
    unset($pdo);
    
    // Response data
    response_with($response);
  break;

  // Return: Object {id, full_name, email, ban}
  case 'search-accounts':
    $by = $data['by'];

    // Conncet to database 
    include connectFile;
    
    switch ($by) {
      # Get by id
      case 'id':
        $account_id = $data['id'];

        ## Get account
        $account_data = getAccount($account_id);
        
        // Executing error
        if( is_null($account_data) ) error('execute');

        // Not exists
        if( $account_data === false ) response_with([]);
        
        // Response with account data
        response_with([$account_data]);
      break;
      
      # Get by email or full_name
      case 'email':
      case 'full_name':
        $search = $by == 'email' ? $data['email'] : $data['full_name'];

        // Search from |< |
        $search .= "%";

        # Create query
        $q = "SELECT `id`, `email`, `full_name`, `entry_date` FROM `accounts` WHERE `". $by ."` LIKE ?";

        // Execute statment
        $stmt = execute_statment($q, [$search]);
        
        // Executing error
        if( is_null($stmt) ) error('execute');
        
        response_with($stmt->fetchAll(PDO::FETCH_ASSOC));
      break;
    }
  break;

  // Returns: bool [true | false]
  case 'ban-account':
    // Get account_id & ban_type & unBlock_at
    $account_id = $data['account_id'];
    $ban_type = $data['ban_type'];
    $unBlock_at = $data['unBlock_at'];

    // Create query
    $q = 'INSERT INTO `ban_details`(`account_id`, `ban_type`, `Unblock_at`) VALUES (?,?,?)';

    // Connect to database
    include connectFile;

    // Execute statment
    $stmt = execute_statment($q, [$account_id, $ban_type, $unBlock_at]);
    
    // Response data
    if( is_null($stmt) ) response_with(false);
    else response_with(true);
  break;

  // Returns: bool [true | false]
  case 'unBlock-account':
    // Get account id
    $account_id = $data['account_id'];

    // Connect to database
    include connectFile;
    
    // Create statment and executed
    $stmt = $pdo->prepare('DELETE FROM `ban_details` WHERE `account_id` = ?');
    $stmt->execute([$account_id]);

    // Response data
    response_with($stmt->rowCount() > 0);
  break;

  // Returns: bool [true | false]
  case 'set-sub':
    // Get data
    $account_id = $data['account_id'];
    $plan_id = $data['plan_id'];
    $cost = $data['cost'];
    $expiry = $data['expiry'];
    $entry_date = date->now_d();
    
    // Connect to database
    include connectFile;

    # Check if sub expired
    $sub_validation = validate_sub($account_id);

    // Response with false if sub not expired
    if( $sub_validation === true ) response_with(false);

    # Delete expired sub
    if( $sub_validation === false ) {

      $stmt = delete_sub($account_id);

      // Executing error
      if( is_null($stmt) ) error('execute');
    }

    // Executing error
    if( gettype($sub_validation) == 'array' ) error('execute');

    # Create sub
    $stmt = set_sub($account_id, $plan_id, $cost, $expiry, $entry_date);

    // Executing error
    if(is_null($stmt)) error('execute');

    # Create sub history

    $sub_id = $pdo->lastInsertId();
    
    $stmt = set_sub_history($sub_id, $account_id, $plan_id, $cost, $expiry);
    
    // Response with true
    response_with(true);
  break;

  // Returns: object | null
  case 'get-sub':
    $account_id = $data['account_id'];

    // Conncet to database
    include connectFile;

    $sub_data = get_sub_data($account_id);

    if( is_null($sub_data) ) error('execute');

    response_with($sub_data);
  break;
  
  // Returns: bool [true | false]
  case 'cancel-sub':
    $account_id = $data['account_id'];

    // Conncet to database
    include connectFile;

    ## Delete sub-history
      $sub_id = get_sub_id($account_id);
      
      if( is_null($sub_id) ) response_with(false);
      
      // Execute statment
      $stmt = delete_sub_history($sub_id);

      // Execute error
      if( is_null($stmt) ) error('execute');
    #

    // Delete sub
    $stmt = delete_sub($account_id);
    
    if(is_null($stmt)) error('execute');
        
    response_with($stmt);
  break;

  // Returns: Array of objects 
  case 'getAll-plans':
    // Conncet to database
    include connectFile;
    
    // Create query
    $plans = getAll_plans();

    if( is_null($plans) ) error('execute');
    
    response_with($plans);
  break;

  // Returns bool
  case 'update-plan':
    $plan_id = $data['plan_id'];
    $price_per_cycle = isset($data['price_per_cycle'])? $data['price_per_cycle'] : null;
    $cycle_duration = isset($data['cycle_duration'])? $data['cycle_duration'] : null;
    $name = isset($data['name'])? $data['name'] : null;
    
    // Conncet to database
    include connectFile;

    // Execute statment
    $stmt = update_plan($plan_id, $price_per_cycle, $cycle_duration, $name);

    // Execute error
    if( is_null($stmt) ) error('execute');
    
    response_with($stmt);
  break;

  // Returns: Array of object
  case 'getAll-subs-history':
    $limit = $data['limit'];
    $except = $data['except'];
    $of_account = !isset($data['of_account'])? null : $data['of_account'];

    // Conncet to database
    include connectFile;

    $subs_data = get_subs_history($limit, $except, $of_account);

    if( is_null($subs_data) ) error('execute');

    response_with($subs_data);
  break;
}