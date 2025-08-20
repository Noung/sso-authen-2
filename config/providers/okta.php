<?php

/** 
 * sso-authen/config/providers/auth0.php
 * *  
 */

require_once __DIR__ . '/../config.php';

return [
    'clientID'     => '0oau9zv751eCqnz3z697',
    'clientSecret' => 'iA5ur13Ol8BSNrRbx554YyLWkZL3bao6t_cxBKMCCfWfNVhUSxgBbiujbM2cGHw0',
    'providerURL'  => 'https://integrator-9662685.okta.com', // providerURL คือ "Okta Domain" ที่ Okta สร้างให้ตอนสมัครสมาชิก

    // **สำคัญ:** path ต้องมี /public/ เพิ่มเข้ามาให้ตรงกับโครงสร้างใหม่
    'redirectUri'  => $absoluteRedirectUri . '/public/callback.php',

    'scopes'       => ['openid', 'profile', 'email'],

    // การแปลงชื่อ Claims จาก PSU SSO ให้เป็นชื่อมาตรฐานที่ Library เราเข้าใจ
    'claim_mapping' => [
        'id'        => 'sub',          // 'sub' (Subject) คือ ID เฉพาะตัวของผู้ใช้ ซึ่งเป็นมาตรฐาน OIDC
        'username'  => 'preferred_username',     // preferred_username ชื่อเล่นหรือชื่อผู้ใช้
        'name'      => 'name',         // 'name' คือชื่อเต็ม
        'firstName' => 'given_name',   // 'given_name' คือชื่อจริง
        'lastName'  => 'family_name',  // 'family_name' คือนามสกุล
        'email'     => 'email',
        'picture'   => 'picture',      // URL รูปโปรไฟล์
        'department' => null            // Auth0 ไม่มีข้อมูลแผนกโดยตรง จึงใส่ null
    ]
];

