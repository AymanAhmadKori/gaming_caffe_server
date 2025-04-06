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

$SERVER_NAME = "localhost";
$USER_NAME = "root";
$PASSWORD = "";

$DBNAME = "game-coffe-mang-db";

try {
  $pdo = new PDO("mysql:host=$SERVER_NAME;dbname=$DBNAME", $USER_NAME, $PASSWORD);
  // ضبط وضع الخطأ في PDO إلى الاستثناءات
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo "Database connection Error: " . $e -> getMessage();
}


function execute_statment(string $q, array $params) {
  global $pdo;

  // Create statment
  $stmt = $pdo->prepare($q);

  // Execute
  try {
    $stmt->execute($params);
    return $stmt;
  } catch(PDOException $e) { return null; }
}

## Accounts
  // Check if account exists in database \\
  function isAccountExists($google_id) {
    global $pdo;

    // Get user data from database \\
    $stmt = execute_statment("SELECT `google_id` FROM accounts WHERE google_id = ?", [$google_id]);

    return $stmt->rowCount() !== 0;
  }

  // Get account data \\
  function getAccountData($google_id, $getOnly_ID = false) {
    global $pdo;

    // Get user data from database \\
    $stmt = $pdo->prepare(
      $getOnly_ID ? 
      'SELECT `id` FROM accounts WHERE google_id = ?'
      : 
      'SELECT `id`, `google_id`, `email`, `full_name` FROM accounts WHERE google_id = ?'
    );

    $stmt->execute([$google_id]);

    $user_data = $stmt->fetch(PDO::FETCH_ASSOC);

    if(is_null($user_data)) return null;

    // Check if Admin's account
    if(
      isset($user_data['google_id']) && 
      $user_data['google_id'] === "102231961515767709745"
    ) $user_data['admin'] = true;
    
    return $user_data;
  }

  // Get ban details 
  function getBanDetails($account_id) {
    global $pdo;
      
    // Get ban details
    $stmt = $pdo->prepare('SELECT `ban_type`, `Unblock_at` FROM `ban_details` WHERE account_id = ?');
    $stmt->execute([$account_id]);
    
    $ban_details = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Return null if account not blocked
    if($stmt->rowCount() == 0) return null;

    // Get reason of ban from ban_type
    $stmt = $pdo->prepare("SELECT `reason_of_ban` FROM `ban_types` WHERE `id` = ?");
    $stmt->execute([$ban_details['ban_type']]);

    // Remove ban_type id
    unset($ban_details['ban_type']);

    // Set reason_of_ban
    $ban_details['reason_of_ban'] = $stmt->fetch(PDO::FETCH_ASSOC)['reason_of_ban'];
      
    return $ban_details;
  }

  // Add new account \\
  function addAccount($google_id, $email, $full_name) {
    global $pdo;

    // Add user in database \\
    $stmt = $pdo->prepare("INSERT INTO
      `accounts` (
        `google_id`,
        `email`,
        `full_name`
      ) 
      VALUES (?,?,?,?)
    ");

    $stmt->execute([$google_id, $email, $full_name]);



    return $stmt->rowCount();
  }

  // Get account subscriptions 
  function getAccount_subs($account_id) {
    global $pdo;

    // Get sub & plan_id
    $stmt = $pdo->prepare("SELECT `plan_id`, `expiry` FROM subs WHERE account_id = ?");
    $stmt->execute([$account_id]);

    // Return null if sub not exsists
    if($stmt->rowCount() == 0) return null;
    
    $sub_data = $stmt->fetch(PDO::FETCH_ASSOC);

    // Check expiry
    $expiry = $sub_data['expiry'];
    if((date->d_to_mil($expiry) - date->now_mil()) <= 0) return null;
    
    // Get plan
    $stmt = $pdo->prepare("SELECT `name` FROM plans WHERE id = ?");
    $stmt->execute([ $sub_data['plan_id'] ]);

    if($stmt->rowCount() == 0) return null;
    
    $plan = $stmt->fetch(PDO::FETCH_ASSOC);

    unset($sub_data['plan_id']);
    
    $sub_data['name'] = $plan['name'];

    return $sub_data;
  }
#

## Subscriptions
  /** Create new subscription
   * @return bool
   * @return null on executing error
  */
  function set_sub(int $account_id, int $plan_id, int|float $cost, $expiry) {
    // Create query
    $q = "INSERT INTO `subs`(`account_id`, `plan_id`, `cost`, `expiry`) VALUES (?,?,?,?)";

    // Execute statment
    $stmt = execute_statment($q, [$account_id, $plan_id, $cost, $expiry]);

    if(is_null($stmt)) return false;
    return true;
  }

  /** Delete subscription using account_id
   * @return bool
   * @return null on executing error
  */
  function delete_sub(int $account_id) {
    
    // Create query
    $q = "DELETE FROM `subs` WHERE `account_id` = ?";

    // Execute statment
    $stmt = execute_statment($q, [$account_id]);

    // Executing error
    if( is_null($stmt) ) return null;

    if($stmt->rowCount() > 0) return true;
    return false;
  }

  /** Get account subscription id
   * @return sub_id[int]
   * @return null if not exsists in database
   * @return array ["error" => "message"] On executing error
   */
  function get_sub_id(int $account_id) {
    // Create qurey
    $q = "SELECT `id` FROM `subs` WHERE `account_id` = ?";

    // Execute statment
    $stmt = execute_statment($q, [$account_id]);

    if( is_null($stmt) ) ['error' => 'Executing Error #001'];


    if($stmt->rowCount() == 0) return null;

    return $stmt->fetch(PDO::FETCH_ASSOC)['id'];
  }

  /** Create new subscription history
   * @return bool
   * @return null on executing error
   */
  function set_sub_history(int $sub_id, int $account_id, int $plan_id, int|float $cost, $expiry) {
    // Create query
    $q = "INSERT INTO `subs_history`(`sub_id`, `account_id`, `plan_id`, `cost`, `expiry`) VALUES (?,?,?,?,?)";

    // Execute statment
    $stmt = execute_statment($q, [$sub_id, $account_id, $plan_id, $cost, $expiry]);

    if( is_null($stmt) ) return null;

    return $stmt->rowCount() > 0;
  }
  
  /** Delete subscription history
   * @return bool
   * @return null on executing error
   */
  function delete_sub_history(int $sub_id) {
    // Create query
    $q = "DELETE FROM `subs_history` WHERE `sub_id` = ?";

    // Execute statment
    $stmt = execute_statment($q, [$sub_id]);

    // Executing error
    if( is_null($stmt) ) return null;

    return $stmt->rowCount() > 0;
  }

  /** Check's if the account has subscription
   * @return bool if exists in database
   * @return null if not exsists in database
   * @return array ["error" => "message"] On executing error
  */
  function validate_sub(int $account_id) {
    // Create query
    $q = "SELECT `expiry` FROM `subs` WHERE `account_id` = ?";

    // Execute statment
    $stmt = execute_statment($q, [$account_id]);

    // Return null if theres an error
    if(is_null($stmt)) return ['error' => 'Executing Error'];

    // Return false
    if($stmt->rowCount() == 0) return -1;

    // Expiry date => milliseconds
    $expiry = date->d_to_mil($stmt->fetch(PDO::FETCH_ASSOC)['expiry']);

    // Now in milliseconds
    $now = date->now_mil();

    // Return false if expired
    if(($expiry - $now) <= 0) return false;

    // Return true
    return true;
  }
#

## Plans
  /** Get all subscriptions plans from database
   * @return array
   * @return null on executing error
  */
  function getAll_plans() {
    // Create query
    $q = "SELECT * FROM `plans`";

    // Execute statment
    $stmt = execute_statment($q, []);

    // Executing error
    if(is_null($stmt)) return null;

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  /** Update plan data
   * 
   * Param value to null for cancel
   * @return bool
   * @return null on executing error
   */
  function update_plan(int $plan_id, int|float|null $price_per_cycle=null, int|null $cycle_duration=null, string|null $name=null) {

    // Create query
    $q = "UPDATE `plans` SET ";
    $fields = [];
    $params = [];


    if (!is_null($price_per_cycle)) {
      $fields[] = '`price_per_cycle` = ?';
      $params[] = $price_per_cycle;
    }
    if (!is_null($cycle_duration)) {
      $fields[] = '`cycle_duration` = ?';
      $params[] = $cycle_duration;
    }
    if (!is_null($name)) {
      $fields[] = '`name` = ?';
      $params[] = $name;
    }

    $q .= implode(', ', $fields);
  
    if( count($params) == 0 ) return null;
    
    $q .= " WHERE `id` = ?";
    $params[] = $plan_id;
    
    // Execute statment
    $stmt = execute_statment($q, $params);

    // Executing error
    if( is_null($stmt) ) return null;

    if($stmt->rowCount() > 0) return true;
    else return false;
  }
#