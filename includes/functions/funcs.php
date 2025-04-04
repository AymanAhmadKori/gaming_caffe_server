<?php 

function addHoursToUTC($hours) {
  // إنشاء كائن DateTime بالتوقيت العالمي UTC
  $dateTime = new DateTime("now", new DateTimeZone("UTC"));
  
  // إضافة عدد الساعات المرسل إلى كائن DateTime
  $interval = new DateInterval("PT" . abs($hours) . "H");
  $dateTime->add($interval);

  // الحصول على الميللي ثانية
  $milliseconds = round(($dateTime->format('u') / 1000));

  // إعادة التاريخ والوقت بتنسيق ISO 8601 مع الميللي ثانية
  return $dateTime->format('Y-m-d\TH:i:s.') . str_pad($milliseconds, 3, '0', STR_PAD_LEFT) . 'Z';
}
function isValidISO8601($datetime) {
  if(empty($datetime)) return false;
  // استخدام التعبير النمطي للتحقق من الصيغة الصحيحة
  $pattern = '/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}(\.\d+)?Z$/';

  if (preg_match($pattern, $datetime)) {
    // محاولة تحويل النص إلى كائن DateTime
    try {
      $date = new DateTime($datetime, new DateTimeZone("UTC"));
      return true;
    } catch (Exception $e) {
      return false;
    }
  }
  return false;
}

function execute_statment($q, $params) {
  global $pdo;

  // Create statment
  $stmt = $pdo->prepare($q);

  // Execute
  try {
    $stmt->execute($params);
    return $stmt;
  } catch(PDOException $e) { return null; }
}