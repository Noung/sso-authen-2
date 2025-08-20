<?php
/** 
 * public/callback.php
 * *  
 */ 

// 1. โหลดคอนฟิกกลาง
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/helpers.php';
// 2. บอก PHP ว่าเราจะใช้ Class SsoHandler
use SsoAuthen\SsoHandler;

try {
    $handler = new SsoHandler($providerConfig);
    // เรียกใช้ความสามารถในการจัดการ Callback
    $internalUser = $handler->handleCallback();

    // หลังจาก Callback สำเร็จ ให้ Redirect ตาม Role ของผู้ใช้
    $role = $internalUser['role'] ?? 'subscriber';
    
    // **หมายเหตุ:** คุณอาจจะต้องสร้างไฟล์เหล่านี้ในแอปพลิเคชันของคุณ
    // if ($role === 'admin') {
    //     header("Location: ".APP_BASE_PATH."/admin_page.php"); 
    // } elseif ($role === 'editor') {
    //     header("Location: ".APP_BASE_PATH."/editor_page.php");
    // } else {
    //     header("Location: ".APP_BASE_PATH."/index.php");
    // }
    // exit;
    if ($role) {
        header("Location: ".APP_BASE_PATH."/index.php"); 
    } 
    exit;

} catch (Exception $e) {
    // die("Error: " . $e->getMessage());
    // เปลี่ยนจาก die() มาใช้ SweetAlert
    render_alert_and_redirect(
        'เกิดข้อผิดพลาด',
        $e->getMessage(),
        'error',
        APP_BASE_PATH . '/index.php' // กลับไปหน้าแรกของแอป
    );
}
