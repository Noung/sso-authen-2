<?php
/** 
 * public/logout.php
 * *  
 */ 

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/helpers.php';
use SsoAuthen\SsoHandler;

// เรียกใช้ความสามารถในการ logout (ซึ่งเป็น static method)
SsoHandler::logout();

// ส่งผู้ใช้กลับไปหน้าแรกของแอปพลิเคชัน
// header("Location: /index.php");
render_alert_and_redirect(
    'ออกจากระบบสำเร็จ',
    'คุณได้ออกจากระบบเรียบร้อยแล้ว',
    'success',
    APP_BASE_PATH . '/index.php' // กลับไปหน้าแรกของแอป
);
// exit;