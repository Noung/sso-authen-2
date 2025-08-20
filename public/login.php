<?php
/** 
 * public/login.php
 * *  
 */

// 1. โหลดคอนฟิกกลาง
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/helpers.php';
// 2. บอก PHP ว่าเราจะใช้ Class SsoHandler
use SsoAuthen\SsoHandler;

try {
    // 3. สร้าง Object จาก Class SsoHandler โดยส่งคอนฟิกเข้าไป
    $handler = new SsoHandler($providerConfig);
    // 4. เรียกใช้ความสามารถในการ login
    $handler->login();
} catch (Exception $e) {
    // กรณีเกิด Error
    // die("Error: " . $e->getMessage());
    // เปลี่ยนจาก die() มาใช้ SweetAlert
    render_alert_and_redirect(
        'เกิดข้อผิดพลาด',
        $e->getMessage(),
        'error',
        APP_BASE_PATH . '/index.php' // กลับไปหน้าแรกของแอป
    );
}