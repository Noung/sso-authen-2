<?php

/** 
 * sso-authen/config/providers/psu.php
 * *  
 */

require_once __DIR__ . '/../config.php';

return [
    'clientID'     => 'qHFUUPlU4z9X11WSyi0FelEjkUhJlT2BNBhpQ98G',
    'clientSecret' => 'NExBAqmQiiMzIwbADiXx1wpDd4sQgx1DrpFlCe9RZQ69luhA9aUBZpaQAbLzFCUJBAgiILrN32RjhZzQtdJfaQGz0mqFTZVeUukOzNv9UDTVPsJJUuJyucgsvJCQQ9Dp',
    'providerURL'  => 'https://psusso-test.psu.ac.th/application/o/oas-test/',

    // **สำคัญ:** path ต้องมี /public/ เพิ่มเข้ามาให้ตรงกับโครงสร้างใหม่
    // 'redirectUri'  => 'http://sso-authen.test/public/callback.php',
    'redirectUri'  => $absoluteRedirectUri . '/public/callback.php',

    'scopes'       => ['openid', 'profile', 'email', 'psu_profile'],

    // การแปลงชื่อ Claims จาก PSU SSO ให้เป็นชื่อมาตรฐานที่ Library เราเข้าใจ
    'claim_mapping' => [
        'id'        => 'psu_id',
        'username'  => 'preferred_username',
        'name'      => 'display_name_th',
        'firstName' => 'first_name_th',
        'lastName'  => 'last_name_th',
        'email'     => 'email',
        'department' => 'department_th'
    ]
];
