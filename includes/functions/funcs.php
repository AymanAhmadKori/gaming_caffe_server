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