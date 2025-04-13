<?php

include 'init.php';

// OAuth 2.0 data
$client_id = "245039045200-35r0eiavk71htmq6np41gf14thiv0tp5.apps.googleusercontent.com";
$client_secret = file_get_contents('secrets/google-OAuth-client_secret.env');
$redirect_uri = "http://localhost:80/projects/github/gaming_caffe_app/login.html";
$admin_redirect_uri = "http://localhost:80/projects/github/gaming_caffe_server/admin-dashboard/login.html";
$token_url = "https://oauth2.googleapis.com/token";

// JWT data
$JWT_life = 24; // Hours
$admin_JWT_life = 2; // Hours

// Get Authorization Code from request
$request = json_decode(file_get_contents('php://input'), true);
$auth_code = $request['code'];
$isAdmin_request = isset($request['admin']);

if (!$auth_code) {
  echo json_encode(["error" => "Authorization Code is missing"]);
  exit();
}

// إرسال طلب POST للحصول على Access Token
$data = [
  'code' => $auth_code,
  'client_id' => $client_id,
  'client_secret' => $client_secret,
  'grant_type' => 'authorization_code',
  'redirect_uri' => $isAdmin_request? $admin_redirect_uri : $redirect_uri,
];

// إعداد cURL لإرسال طلب POST إلى Google Token Endpoint
$ch = curl_init($token_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
  'Content-Type: application/x-www-form-urlencoded'
]);

// تنفيذ الطلب واستلام الاستجابة
$response = curl_exec($ch);

// تحقق من وجود خطأ
if (curl_errno($ch)) {
  echo json_encode(["error" => curl_error($ch)]);
  exit;
}

curl_close($ch);

// تحويل الاستجابة من JSON إلى مصفوفة PHP
$response_data = json_decode($response, true);

if(!isset($response_data['access_token'])){
  echo json_encode(["error" => "Could not retrieve access token"]);
  exit();
}

// تحقق إذا تم الحصول على الـ Access Token
$access_token = $response_data['access_token'];

// الآن نستخدم الـ Access Token للوصول إلى بيانات المستخدم
$api_url = "https://www.googleapis.com/oauth2/v2/userinfo";  // API للحصول على بيانات المستخدم

$ch = curl_init($api_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
  "Authorization: Bearer $access_token"  // إضافة الـ Access Token في رأس الطلب
]);

// تنفيذ الطلب للحصول على بيانات المستخدم
$user_response = curl_exec($ch);

if (curl_errno($ch)) {
  echo json_encode(["error" => curl_error($ch)]);
  exit;
}

curl_close($ch);

// تحويل الاستجابة من JSON إلى مصفوفة PHP
$google_data = json_decode($user_response, true);


// Check if google_id exsists \\
if(!isset($google_data['id'])) {
  echo json_encode(["error" => "Could not retrieve user data"]);
  exit();
}

// Connect to database\\
include 'connect.php';

// Get account data from database \\
$account_data = getAccountData($google_data['id']);

// Compare & Update account data, If account exsists \\
if(!is_null($account_data)) {
  // Array to store update fields
  $updates = [];
  $params = [];

  // Compare old and new data
  if ($account_data['email'] !== $google_data['email']) {
    $updates[] = "email = ?";
    $params[] = $google_data['email'];

    $account_data['email'] = $google_data['email'];
  }
  if ($account_data['full_name'] !== $google_data['name']) {
    $updates[] = "full_name = ?";
    $params[] = $google_data['name'];
    
    $account_data['full_name'] = $google_data['name'];
  }

  // Generate SQL update statement if there are changes
  if (!empty($updates)) {
    $sql = "UPDATE accounts SET " . implode(", ", $updates) . " WHERE google_id = ?";
    $params[] = $account_data['google_id']; // Add old google?id as the WHERE condition parameter

    // Update new account_data from google_data \\
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
  }

  // Remove google_id & id
  unset($account_data['google_id']);
} else {
  // Add account in database \\
  addAccount($google_data['id'], $google_data['email'], $google_data['name']);  

  $account_id = $pdo->lastInsertId();

  // Set default subscription
  set_default_sub($account_id);

  $account_data = [
    'id' => $account_id,
    "google_id" => $google_data['id'],
    "email" => $google_data['email'],
    "full_name" => $google_data['name']
  ];
}

// Set JWT expiry
if(isset($account_data['admin'])) {
  $expiry = addHoursToUTC($admin_JWT_life);
} else {
  $expiry = addHoursToUTC($JWT_life);
}

// Respond with JWT \\
$payload = json_encode([
  ...$account_data,
  'sub' => getAccount_subs($account_data['id']),
  'ban' => getBanDetails($account_data['id']),
  'expiry' => $expiry
]);

echo createJWT($payload);
exit();