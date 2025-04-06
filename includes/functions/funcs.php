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

class DateFuncs {

  /** Day => ISO format */
  public function d_to_iso($date) {
    $dt = new DateTime($date, new DateTimeZone("UTC"));
    return $dt->format(DateTime::ATOM); // ISO 8601
  }
  /** Date => Milliseconds */
  public function d_to_mil($date) {
    $dt = new DateTime($date, new DateTimeZone("UTC"));
    return $dt->getTimestamp() * 1000;
  }

  /** ISO format => Date */
  public function iso_to_d($iso) {
      $dt = new DateTime($iso, new DateTimeZone("UTC"));
      return $dt->format('Y-m-d H:i:s');
  }
  /** ISO format => Milliseconds */
  public function iso_to_mil($iso) {
      $dt = new DateTime($iso, new DateTimeZone("UTC"));
      return $dt->getTimestamp() * 1000;
  }

  /** Milliseconds => Date */
  public function mil_to_d($milliseconds) {
    $seconds = $milliseconds / 1000;
    $dt = new DateTime("@$seconds"); // الـ @ تنشئ من Unix timestamp
    $dt->setTimezone(new DateTimeZone("UTC"));
    return $dt->format('Y-m-d H:i:s');
  }
  /** Milliseconds => ISO format */
  public function mil_to_iso($milliseconds) {
      $seconds = $milliseconds / 1000;
      $dt = new DateTime("@$seconds"); // الـ @ تنشئ من Unix timestamp
      $dt->setTimezone(new DateTimeZone("UTC"));
      return $dt->format(DateTime::ATOM); // ISO 8601
  }

  // Current UTC
  /** Now Date */
  public function now_d() {
    $dt = new DateTime("now", new DateTimeZone("UTC"));
    return $dt->format('Y-m-d H:i:s');
  }
  /** Now in Milliseconds */
  public function now_mil() {
    $dt = new DateTime("now", new DateTimeZone("UTC"));
    return $dt->getTimestamp() * 1000;
  }
  /** Now in ISO format */
  public function now_iso() {
    $dt = new DateTime("now", new DateTimeZone("UTC"));
    return $dt->format(DateTime::ATOM); // ISO 8601
  }
}
define('date', new DateFuncs());