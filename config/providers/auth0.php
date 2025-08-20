<?php

/** 
 * sso-authen/config/providers/auth0.php
 * *  
 */

require_once __DIR__ . '/../config.php';

return [
    'clientID'     => 'KNBxmKAw6XNS1Je0ep9xW8D7g7dZMDA8',
    'clientSecret' => 'HxK2QUQ4bVAMTIyIlvRUq8T4D82Sht7-bAIaZODhbNna3k3dsZw5iYyLMCDEtUR9',
    'providerURL'  => 'https://dev-ihvejgde0lo18on2.us.auth0.com',

    // **สำคัญ:** path ต้องมี /public/ เพิ่มเข้ามาให้ตรงกับโครงสร้างใหม่
    'redirectUri'  => $absoluteRedirectUri . '/public/callback.php',

    'scopes'       => ['openid', 'profile', 'email'],

    // การแปลงชื่อ Claims จาก PSU SSO ให้เป็นชื่อมาตรฐานที่ Library เราเข้าใจ
    'claim_mapping' => [
        'id'        => 'sub',          // 'sub' (Subject) คือ ID เฉพาะตัวของผู้ใช้ ซึ่งเป็นมาตรฐาน OIDC
        'username'  => 'nickname',     // 'nickname' คือชื่อเล่นหรือชื่อผู้ใช้
        'name'      => 'name',         // 'name' คือชื่อเต็ม
        'firstName' => 'given_name',   // 'given_name' คือชื่อจริง
        'lastName'  => 'family_name',  // 'family_name' คือนามสกุล
        'email'     => 'email',
        'picture'   => 'picture',      // URL รูปโปรไฟล์
        'department' => null            // Auth0 ไม่มีข้อมูลแผนกโดยตรง จึงใส่ null
    ]
];
