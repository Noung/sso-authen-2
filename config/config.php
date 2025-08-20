<?php

/**
 * sso-authen/config/config.php
 * * ไฟล์ตั้งค่าหลัก (Main Configuration File) ทำหน้าที่โหลดการตั้งค่าทั้งหมดที่จำเป็นสำหรับ SSO Handler
 */

// APP_BASE_PATH กำหนด Path หลักของแอปพลิเคชัน (ใช้สำหรับสร้าง URL ภายใน)
// ถ้าแอปอยู่ที่ root ของโดเมน ให้ใส่เป็นค่าว่าง '' ถ้าแอปอยู่ในโฟลเดอร์ย่อย ให้ใส่ / ตามด้วยชื่อโฟลเดอร์ เช่น '/sso-authen' (กรณี Virtual Host ของ Laragon ให้เว้นว่างเช่นกัน)
define('APP_BASE_PATH', '');

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

// --- ส่วนตั้งค่าหลัก ---

// 3. เลือกว่าจะใช้ Provider (มหาวิทยาลัย) ไหน
// ในอนาคต หากต้องการเปลี่ยนไปใช้ของมหาวิทยาลัยอื่น ก็แค่มาเปลี่ยนค่าตรงนี้
$activeProvider = 'auth0';

// 4. โหลดไฟล์ตั้งค่าของ Provider ที่เลือก
$providerConfigFile = __DIR__ . '/providers/' . $activeProvider . '.php';

if (!file_exists($providerConfigFile)) {
    die("Error: Configuration file for provider '{$activeProvider}' not found.");
}
$providerConfig = require_once $providerConfigFile;


// 5. โหลดไฟล์จัดการ User Handler (user_handler.php) ของแอปพลิเคชันที่เรียกใช้ sso-authen
// ไฟล์นี้เป็นส่วนที่แอปพลิเคชันที่นำไลบรารีไปใช้ต้องเป็นคนสร้าง
// **ข้อควรระวัง:** Path นี้อาจต้องปรับเปลี่ยนตามโครงสร้างของแอปพลิเคชันที่นำไปใช้จริง
$userHandlerPath = __DIR__ . '/../user_handler.php';
if (file_exists($userHandlerPath)) {
    require_once $userHandlerPath;
}
