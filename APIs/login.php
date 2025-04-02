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

// Get user data from database \\
$user_data = getUserData($google_data['id']);

// Compare & Update user data, If user exsists \\
if(!is_null($user_data)) {
  // Array to store update fields
  $updates = [];
  $params = [];

  // Compare old and new data
  if ($user_data['email'] !== $google_data['email']) {
    $updates[] = "email = ?";
    $params[] = $google_data['email'];

    $user_data['email'] = $google_data['email'];
  }
  if ($user_data['full_name'] !== $google_data['name']) {
    $updates[] = "full_name = ?";
    $params[] = $google_data['name'];
    
    $user_data['full_name'] = $google_data['name'];
  }

  // Generate SQL update statement if there are changes
  if (!empty($updates)) {
    $sql = "UPDATE accounts SET " . implode(", ", $updates) . " WHERE google_id = ?";
    $params[] = $user_data['google_id']; // Add old google?id as the WHERE condition parameter

    // Update new user_data from google_data \\
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
  }

  // Remove google_id & id
  unset($user_data['google_id'], $user_data['id']);
} else {
  // Add user in database \\
  addUser($google_data['id'], $google_data['email'], $google_data['name']);

  // Save user data \\
  $user_data = [
    'email' => $google_data['email'],
    'full_name' => $google_data['name'],
    'profile_image' => $google_data['picture'],
    'ban' => null
  ];
}

// Set JWT expiry
if(isset($user_data['admin'])) {
  $expiry = addHoursToUTC($admin_JWT_life);
} else {
  $expiry = addHoursToUTC($JWT_life);
}

// Respond with JWT \\
$payload = json_encode([
  ...$user_data,
  "profile_image" => $google_data['picture'],
  'sub' => getUser_subs($google_data['id']),
  'ban' => getBanDetails($user_data['ban']),
  'expiry' => $expiry
]);

echo createJWT($payload);
exit();