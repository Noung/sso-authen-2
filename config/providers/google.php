<?php

/** 
 * sso-authen/config/providers/google.php
 * *  
 */

require_once __DIR__ . '/../config.php';

return [
    'clientID'     => '841929617973-4mld7p3iich53cou6aor1dunipnumtqq.apps.googleusercontent.com',
    'clientSecret' => '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',
    'providerURL'  => 'https://accounts.google.com', // นี่คือ Issuer URL ของ Google

    // **สำคัญ:** path ต้องมี /public/ เพิ่มเข้ามาให้ตรงกับโครงสร้างใหม่
    'redirectUri'  => $absoluteRedirectUri . '/public/callback.php',

    'scopes'       => ['openid', 'profile', 'email'], // Scopes มาตรฐานของ Google

    // แปลงชื่อ Claims จาก Google ให้เป็นชื่อมาตรฐานที่ Library เราเข้าใจ
    'claim_mapping' => [
        'id'        => 'sub', // Google ใช้ 'sub' เป็น User ID
        'username'  => 'email', // ใช้ email แทน username
        'name'      => 'name',
        'firstName' => 'given_name',
        'lastName'  => 'family_name',
        'email'     => 'email',
        'department' => null // Google ไม่มีข้อมูลแผนก
    ]
];
