# **คู่มือการติดตั้งและใช้งาน sso-authen Library for PHP**

`sso-authen` เป็นไลบรารี PHP ขนาดเล็กสำหรับเชื่อมต่อเว็บแอปพลิเคชันเข้ากับผู้ให้บริการยืนยันตัวตน (Identity Provider) ที่ใช้มาตรฐาน OpenID Connect (OIDC) ถูกออกแบบมาให้มีความยืดหยุ่นสูง สามารถรองรับผู้ให้บริการได้หลายราย (Multi-Provider) และมีสถาปัตยกรรมที่สะอาดสำหรับใช้งานในเว็บแอปพลิเคชัน PHP สมัยใหม่

---

## ✨ คุณสมบัติ (Features)

* **Modern PHP:** พัฒนาบนพื้นฐานของ PHP 7.0+
* **Composer Ready:** จัดการ Dependencies ทั้งหมดผ่าน Composer
* **Clean Architecture:** ใช้สถาปัตยกรรมเชิงวัตถุ (OOP) โดยมี `SsoHandler` เป็นคลาสหลักในการทำงาน
* **Multi-Provider Support:** รองรับการเชื่อมต่อกับผู้ให้บริการ OIDC หลายรายผ่านระบบคอนฟิกที่ยืดหยุ่น
* **Data Normalization:** มีระบบแปลงข้อมูล (Claim Mapping) เพื่อให้แอปพลิเคชันได้รับข้อมูลผู้ใช้ในรูปแบบมาตรฐานเดียวกันเสมอ ไม่ว่าจะล็อกอินมาจากที่ใด
* **Decoupled:** แยกส่วนของไลบรารี (Authentication) ออกจากตรรกะของแอปพลิเคชัน (Authorization) อย่างชัดเจนผ่าน "สัญญาใจ" (`user_handler.php`)
* **User-Friendly Feedback:** มีระบบแสดงผลข้อความโต้ตอบที่สวยงามด้วย SweetAlert2

---

## 📋 ข้อกำหนด (Requirements)

* **PHP:** เวอร์ชัน 7.0 หรือสูงกว่า
* **Composer:** ติดตั้งและพร้อมใช้งานใน Command Line
* **Web Server:** Apache หรือ Nginx พร้อม PHP (เช่น Laragon, XAMPP)
* **PHP Extensions:** cURL, JSON
* **Credentials:** `Client ID` และ `Client Secret` ที่ได้รับจากการลงทะเบียนแอปพลิเคชันกับผู้ให้บริการ OIDC

---

## 📁 โครงสร้างไฟล์ (Files Structure)

โปรเจกต์ที่นำไลบรารีนี้ไปใช้ ควรมีโครงสร้างโดยรวมดังนี้:

```text
/your-webapp/                      <-- โฟลเดอร์หลักของเว็บแอปพลิเคชัน
|
|-- sso-authen/                     <-- โฟลเดอร์ของไลบรารี
|   |-- config/
|   |   |-- config.php
|   |   `-- providers/
|   |       `-- psu.php
|   |-- public/
|   |   |-- callback.php
|   |   |-- login.php
|   |   |-- logout.php
|   |   |-- helpers.php
|   |   `-- templates/
|   |       `-- layout.php
|   `-- src/
|       `-- SsoHandler.php
|
|-- vendor/                         (สร้างโดย Composer)
|-- composer.json
|-- db_config.php                   (ไฟล์ตั้งค่า DB ของแอป)
|-- user_handler.php                (ไฟล์ "สัญญาใจ" ของแอป)
`-- index.php                       (และไฟล์อื่นๆ ของแอป)
```

---

## 🚀 การติดตั้งและตั้งค่า (Installation & Configuration)

### 1. ติดตั้งไลบรารีด้วย Composer

คัดลอกโฟลเดอร์ `sso-authen` ทั้งหมดไปวางไว้ในโปรเจกต์เว็บแอปพลิเคชันของคุณ จากนั้นใน Terminal ให้เข้าไปที่ไดเรกทอรี `sso-authen` แล้วรันคำสั่ง:

```bash
composer install
```

*(หากคุณมีไฟล์ `composer.lock` อยู่แล้ว) หรือ `composer update` เพื่อติดตั้ง Dependencies ทั้งหมด*

### 2. ตั้งค่า Autoload

ที่ sso-authen เปิดไฟล์ `composer.json` ของ แล้วเพิ่มการตั้งค่า `autoload` เพื่อให้ Composer รู้จักคลาส `SsoHandler` ของเรา:

```json
{
    "require": {
        "jumbojett/openid-connect-php": "^0.9.5"
    },
    "autoload": {
        "psr-4": {
            "SsoAuthen\\": "sso-authen/src/"
        }
    }
}
```
จากนั้นรันคำสั่ง `composer dump-autoload` ใน Terminal

### 3. เพิ่ม Provider ใหม่ (ถ้าจำเป็น)

หากต้องการเชื่อมต่อกับผู้ให้บริการรายใหม่ (เช่น มหาวิทยาลัยอื่น) ให้สร้างไฟล์คอนฟิกสำหรับผู้ให้บริการแต่ละรายใน `sso-authen/config/providers/` โดยใช้ psu.php เป็นต้นแบบ และแก้ไขค่า `clientID`, `clientSecret`, `providerURL`, `redirectUri`, `scopes`, และ `claim_mapping` ให้ถูกต้อง 

ตัวอย่าง **psu.php**:

```php
<?php
// sso-authen/config/providers/psu.php
return [
    'clientID'     => 'YOUR_PSU_CLIENT_ID_HERE',
    'clientSecret' => 'YOUR_PSU_CLIENT_SECRET_HERE',
    'providerURL'  => '[https://psusso.psu.ac.th/](https://psusso.psu.ac.th/)...',
    'redirectUri'  => '[http://your-app.test/sso-authen/public/callback.php](http://your-app.test/sso-authen/public/callback.php)', 
    'scopes'       => ['openid', 'profile', 'email', 'psu_profile'],
    'claim_mapping' => [
        'id'        => 'psu_id',
        'username'  => 'preferred_username',
        'name'      => 'display_name_th',
        'email'     => 'email',
        // ... map fields as needed ...
    ]
];
```

### 4. ตั้งค่าไลบรารี

เปิดไฟล์ `sso-authen/config/config.php` และกำหนดค่าต่อไปนี้:
* `APP_BASE_PATH`: กำหนด Path หลักของแอปพลิเคชันที่ใช้ sso-authen ให้ถูกต้อง
* `$activeProvider`: เปลี่ยนชื่อให้ตรงกับไฟล์ของผู้ให้บริการที่ต้องการใช้ (เช่น 'psu')
* `$userHandlerPath`: ตรวจสอบ Path ที่เรียกใช้ `user_handler.php` ให้ถูกต้อง (เช่น __DIR__ . '/../../user_handler.php')

---

## 🔌 การนำไปใช้งาน (Usage)

### 1. ตั้งค่าการเชื่อมต่อฐานข้อมูล (สำหรับแอปพลิเคชัน ถ้ายังไม่มี)

สร้างไฟล์ `db_config.php` ใน root ของเว็บแอปพลิเคชัน เพื่อเก็บข้อมูลการเชื่อมต่อฐานข้อมูล

```php
<?php // db_config.php
define('DB_HOST', 'localhost');
define('DB_NAME', 'your_database_name');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');
```

### 2. สร้างตารางสมาชิก (สำหรับแอปพลิเคชัน ถ้ายังไม่มี)

รันคำสั่ง SQL นี้ในฐานข้อมูลของแอปพลิเคชันเพื่อสร้างตาราง `users`

```sql
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` varchar(255) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `role` varchar(50) NOT NULL DEFAULT 'subscriber',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### 3. สร้างฟังก์ชัน "สัญญาใจ" (user_handler.php)

ในโปรเจกต์หลักของคุณ (your-webapp) สร้างไฟล์ `user_handler.php` ที่มีฟังก์ชัน `findOrCreateUser()` เพื่อค้นหาหรือสร้างผู้ใช้ตามข้อมูลที่ได้รับจาก SSO เพื่อจัดการกับฐานข้อมูลสมาชิกของแอปพลิเคชันคุณเอง

```php
<?php
/**
 * www/user_handler.php
 * เทมเพลตสำหรับจัดการข้อมูลผู้ใช้ในฐานข้อมูลของแอปพลิเคชัน
 * นี่คือไฟล์ "สัญญาใจ" ที่แต่ละแอปพลิเคชันต้องสร้างขึ้นเอง
 */

/**
 * ค้นหาผู้ใช้จากฐานข้อมูลด้วยข้อมูลจาก SSO หากไม่พบจะสร้างผู้ใช้ใหม่
 *
 * @param array $normalizedUser ข้อมูลผู้ใช้ที่ผ่านการแปลงชื่อฟิลด์เป็นมาตรฐานแล้ว
 * @param object $ssoUserInfo ข้อมูลผู้ใช้ดิบที่ได้จาก Provider SSO
 * @return array ข้อมูลผู้ใช้จากฐานข้อมูลภายในของแอปพลิเคชัน (รวม role)
 */
function findOrCreateUser(array $normalizedUser, object $ssoUserInfo): array {
    // 1. เรียกใช้ไฟล์ตั้งค่าฐานข้อมูล
    require_once __DIR__ . '/db_config.php';

    // 2. ตั้งค่าการเชื่อมต่อ PDO
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    try {
        // 3. เชื่อมต่อฐานข้อมูล
        $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);

        // 4. ค้นหาผู้ใช้จากอีเมล (หรือ user_id ถ้าต้องการ)
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$normalizedUser['email']]);
        $user = $stmt->fetch();

        if ($user) {
            // --- กรณีที่ 1: พบผู้ใช้ในระบบ (สมาชิกเก่า) ---

            // อัปเดตข้อมูลล่าสุด (เผื่อมีการเปลี่ยนชื่อ-สกุล)
            $updateStmt = $pdo->prepare(
                "UPDATE users SET name = ?, user_id = ? WHERE id = ?"
            );
            $updateStmt->execute([
                $normalizedUser['name'],
                $normalizedUser['id'], // user_id
                $user['id']
            ]);
            
            // คืนค่าข้อมูลผู้ใช้จากฐานข้อมูลของเรา
            return $user;

        } else {
            // --- กรณีที่ 2: ไม่พบผู้ใช้ในระบบ (สมาชิกใหม่) ---

            // กำหนด Role เริ่มต้น
            $defaultRole = 'subscriber';

            // เตรียมข้อมูลและสร้างผู้ใช้ใหม่
            $insertStmt = $pdo->prepare(
                "INSERT INTO users (user_id, email, name, role) VALUES (?, ?, ?, ?)"
            );
            $insertStmt->execute([
                $normalizedUser['id'], // user_id
                $normalizedUser['email'],
                $normalizedUser['name'],
                $defaultRole
            ]);

            $newUserId = $pdo->lastInsertId();

            // คืนค่าข้อมูลผู้ใช้ใหม่ที่เพิ่งสร้าง
            return [
                'id' => $newUserId,
                'user_id' => $normalizedUser['id'],
                'email' => $normalizedUser['email'],
                'name' => $normalizedUser['name'],
                'role' => $defaultRole
            ];
        }

    } catch (\PDOException $e) {
        // หากมีปัญหาในการเชื่อมต่อหรือคิวรีฐานข้อมูล ให้หยุดการทำงานและแสดงข้อผิดพลาด
        // ในระบบจริง ควรจะบันทึก Log แทนการ die()
        throw new \PDOException($e->getMessage(), (int)$e->getCode());
    }
}
```

### 4. การสร้างลิงก์ Login / Logout

ในหน้าเว็บแอปพลิเคชันของคุณ ให้สร้างลิงก์ไปยังไฟล์ใน sso-authen/public/
* Login ลิงค์ไปยัง `/sso-authen/public/login.php` 
* Logout ลิงค์ไปยัง `/sso-authen/public/logout.php`

### 5. การป้องกันหน้าเพจ (Auth Guard)

สำหรับทุกหน้าที่ต้องการการยืนยันตัวตน ให้เพิ่มโค้ด "ยามเฝ้าประตู" (Auth Guard) ไว้ที่บรรทัดบนสุดของไฟล์

ตัวอย่าง:

```php
<?php
// protected_page.php

if (!session_id()) {
    session_start();
}

// ตรวจสอบสถานะการล็อกอิน
if (!isset($_SESSION['user_is_logged_in']) || !$_SESSION['user_is_logged_in']) {
    // ถ้ายังไม่ได้ล็อกอิน ให้ส่งไปหน้า login ของไลบรารี
    header("Location: /sso-authen/public/login.php");
    exit;
}

// หากล็อกอินแล้ว สามารถดึงข้อมูลผู้ใช้จาก Session มาใช้งานได้
$currentUser = $_SESSION['user_info'];
echo "ยินดีต้อนรับ, " . htmlspecialchars($currentUser['name']);
```

### 6. การ Redirect ตาม Role

ไลบรารีจะทำการ Redirect ผู้ใช้หลังจากล็อกอินสำเร็จโดยอัตโนมัติ คุณสามารถแก้ไขปลายทางได้ที่ไฟล์ `sso-authen/public/callback.php` ตาม Role ที่ได้รับมาจาก `user_handler.php`

---

### 💡 บันทึกทางเทคนิค (Technical Notes)
* Dependency Management: ใช้ Composer เป็นเครื่องมือหลักในการจัดการ Library ภายนอกทั้งหมด
* Architecture: `SsoHandler` Class ทำหน้าที่เป็นแกนหลักในการประมวลผล ส่วน config ถูกแยกออกมาเพื่อความยืดหยุ่น
* Data Normalization: `claim_mapping` ในไฟล์คอนฟิกของ Provider แต่ละราย คือหัวใจที่ทำให้ไลบรารีรองรับหลายผู้ให้บริการได้ โดยจะแปลงข้อมูลที่ได้รับมาให้เป็นรูปแบบมาตรฐานเดียวกัน
* Application Contract: ฟังก์ชัน `findOrCreateUser()` ใน `user_handler.php` เป็น "สัญญาใจ" ที่แยกระหว่างหน้าที่ของไลบรารี (Authentication) กับหน้าที่ของแอปพลิเคชัน (Authorization) อย่างชัดเจน
