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

// Check if account exists in database \\
function isAccountExists($google_id) {
  global $pdo;

  // Get user data from database \\
  $stmt = $pdo->prepare('SELECT `google_id` FROM accounts WHERE google_id = ?');
  $stmt->execute([$google_id]);

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

  // Get plan
  $stmt = $pdo->prepare("SELECT `name` FROM plans WHERE id = ?");
  $stmt->execute([ $sub_data['plan_id'] ]);

  if($stmt->rowCount() == 0) return null;
  
  $plan = $stmt->fetch(PDO::FETCH_ASSOC);

  unset($sub_data['plan_id']);
  
  $sub_data['name'] = $plan['name'];

  return $sub_data;
}