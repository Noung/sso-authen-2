<?php

/**
 * sso-authen/config/config.php
 * * ไฟล์ตั้งค่าหลัก (Main Configuration File) ทำหน้าที่โหลดการตั้งค่าทั้งหมดที่จำเป็นสำหรับ SSO Handler
 */

// APP_BASE_PATH กำหนด Path หลักของแอปพลิเคชัน (ใช้สำหรับสร้าง URL ภายใน)
// ถ้าแอปอยู่ที่ root ของโดเมน ให้ใส่เป็นค่าว่าง '' ถ้าแอปอยู่ในโฟลเดอร์ย่อย ให้ใส่ / ตามด้วยชื่อโฟลเดอร์ เช่น '/sso-authen' (กรณี Virtual Host ของ Laragon ให้เว้นว่างเช่นกัน)
define('APP_BASE_PATH', '/');

// โดยปกติ Provider จะใช้ Absolute path (URL แบบเต็ม) เท่านั้น ()ขึ้นอยู่กับการสร้าง URIs Redirect) โดย $absoluteRedirectUri จะถูกนำไปใช้ใน redirectUri ของหน้า callback เพื่อ redirect หลังจาก login สำเร็จ 
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
$host = $_SERVER['HTTP_HOST'];
$absoluteRedirectUri = $protocol . $host . APP_BASE_PATH;

// 1. โหลด Autoloader ของ Composer
// ทำให้เราสามารถเรียกใช้ Class จาก Library ทั้งหมดได้โดยอัตโนมัติ
require_once __DIR__ . '/../vendor/autoload.php';

// 2. เริ่มการทำงานของ Session มาตรฐาน PHP
if (!session_id()) {
    session_start();
}

// 3. เลือกว่าจะใช้ Provider (มหาวิทยาลัย) ไหน
// ในอนาคต หากต้องการเปลี่ยนไปใช้ของมหาวิทยาลัยอื่น ก็แค่มาเปลี่ยนค่าตรงนี้
$activeProvider = 'psu';

// 4. โหลดไฟล์ตั้งค่าของ Provider ที่เลือก
$providerConfigFile = __DIR__ . '/providers/' . $activeProvider . '.php';

if (!file_exists($providerConfigFile)) {
    die("Error: Configuration file for provider '{$activeProvider}' not found.");
}
$providerConfig = require_once $providerConfigFile;

/**
 * ----------------------------------------------------------------------
 * การตั้งค่า User Handler Endpoint (สำหรับเวอร์ชั่น 2)
 * ----------------------------------------------------------------------
 * หากคุณต้องการให้ sso-authen เรียกไปยัง API Endpoint เพื่อจัดการข้อมูลผู้ใช้ แทนการใช้ user_handler.php แบบเดิม ให้กำหนด URL ของ Endpoint ของคุณที่นี่ หากค่านี้เป็นค่าว่าง (null) ระบบจะกลับไปใช้ user_handler.php ตามปกติ
 *
 * ตัวอย่าง:
 * define('USER_HANDLER_ENDPOINT', 'https://yourapp.com/api/sso_user');
 */
define('USER_HANDLER_ENDPOINT', null); // ค่าเริ่มต้นคือ null เพื่อให้ใช้แบบเดิมได้
define('API_SECRET_KEY', 'YOUR_STRONG_SECRET_KEY'); // <-- **สำคัญ:** API Secret Key เพื่อความปลอดภัย ควรตั้งค่า Secret Key ที่จะใช้ในการยืนยันตัวตนระหว่าง sso-authen library และเว็บแอปพลิเคชันของคุณ Key นี้จะถูกส่งไปใน HTTP Header 'X-API-SECRET'
