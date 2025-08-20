/**
 * file: your-nodejs-app/routes/ssoHandler.js
 * * เทมเพลต API Endpoint สำหรับเว็บแอปพลิเคชัน Node.js
 * เพื่อรับข้อมูลจาก sso-authen library
 * * การติดตั้งที่จำเป็น:
 * npm install express
 * npm install express-session (หากต้องการใช้ Session)
 * npm install jsonwebtoken (หากต้องการใช้ JWT)
 */

const express = require('express');
const router = express.Router();

// --- การตั้งค่าที่ต้องทำใน Web Application ของคุณ ---
// เพื่อความปลอดภัยสูงสุด ควรเก็บค่านี้ไว้ใน Environment Variables (.env)
// และต้องเป็นค่าเดียวกันกับที่ตั้งไว้ใน config.php ของ sso-authen
const APP_API_SECRET_KEY = 'YOUR_STRONG_SECRET_KEY'; 

/**
 * ------------------------------------------------------------------
 * Middleware สำหรับตรวจสอบ API Secret Key
 * ------------------------------------------------------------------
 * เป็นการสร้าง "ยามเฝ้าประตู" ให้กับ Endpoint ของเรา
 * ทุก Request ที่เข้ามาจะถูกตรวจสอบก่อนเสมอ
 */
const verifyApiSecret = (req, res, next) => {
    const receivedKey = req.headers['x-api-secret'];
    if (receivedKey && receivedKey === APP_API_SECRET_KEY) {
        next(); // Key ถูกต้อง, อนุญาตให้ไปต่อ
    } else {
        console.error('Authentication Error: Invalid or missing API Secret Key.');
        res.status(401).json({ error: 'Unauthorized' }); // Key ไม่ถูกต้อง, ปฏิเสธ Request
    }
};

/**
 * ------------------------------------------------------------------
 * POST /api/sso-handler
 * ------------------------------------------------------------------
 * นี่คือ Endpoint หลักที่ sso-authen จะเรียกเข้ามา
 * เราใช้ middleware `verifyApiSecret` เพื่อตรวจสอบ Key ก่อนเสมอ
 */
router.post('/sso-handler', verifyApiSecret, async (req, res) => {
    try {
        // 1. รับข้อมูล JSON จาก Request Body
        // (Express.json() middleware จะแปลง JSON string เป็น Object ให้เราอัตโนมัติ)
        const { normalizedUser, ssoUserInfo } = req.body;

        if (!normalizedUser) {
            return res.status(400).json({ error: 'Invalid JSON payload: normalizedUser is missing.' });
        }

        console.log('Received normalized user data:', normalizedUser);

        // 2. เรียกใช้ฟังก์ชันจัดการข้อมูลผู้ใช้ในฐานข้อมูลของคุณ
        //    (คุณต้องสร้างฟังก์ชันนี้ขึ้นมาเองเพื่อเชื่อมต่อกับ DB ของคุณ เช่น MongoDB, PostgreSQL)
        const internalUser = await findOrCreateUserInDb(normalizedUser);
        
        // 3. (สำคัญมาก) สร้าง Session หรือ Token ของเว็บแอปพลิเคชัน Node.js
        //    ณ จุดนี้ คือจุดที่คุณต้องทำให้ผู้ใช้ "ล็อกอินค้าง" ในระบบของคุณ
        //    เลือกใช้วิธีใดวิธีหนึ่ง:

        //    - ตัวอย่างการใช้ Session (ต้องติดตั้ง express-session):
        //    req.session.user = {
        //        id: internalUser.id,
        //        email: internalUser.email,
        //        role: internalUser.role
        //    };
        
        //    - ตัวอย่างการสร้าง JWT Token (ต้องติดตั้ง jsonwebtoken):
        //    const token = jwt.sign({ id: internalUser.id, role: internalUser.role }, 'YOUR_JWT_SECRET', { expiresIn: '1h' });
        //    // จากนั้นคุณอาจจะต้องส่ง Token นี้กลับไปให้ Client ผ่าน Cookie หรือวิธีอื่นต่อไป

        console.log('User processed successfully:', internalUser);

        // 4. ส่งข้อมูลผู้ใช้ในระบบของคุณกลับไปให้ sso-authen library
        res.status(200).json(internalUser);

    } catch (error) {
        console.error('Error processing user data:', error);
        res.status(500).json({ error: 'Internal Server Error' });
    }
});

/**
 * ------------------------------------------------------------------
 * (ตัวอย่าง) ฟังก์ชันสำหรับจัดการข้อมูลผู้ใช้ในฐานข้อมูล
 * ------------------------------------------------------------------
 * คุณต้องแก้ไขส่วนนี้ให้เชื่อมต่อกับฐานข้อมูลจริงของคุณ
 * @param {object} normalizedUser - ข้อมูลผู้ใช้ที่ได้รับจาก sso-authen
 * @returns {Promise<object>} - ข้อมูลผู้ใช้จากฐานข้อมูลของคุณ
 */
async function findOrCreateUserInDb(normalizedUser) {
    // TODO: เขียนโค้ดเชื่อมต่อกับฐานข้อมูลของคุณที่นี่ (เช่น MongoDB, MySQL, PostgreSQL)
    
    // ตัวอย่าง Logic (จำลอง):
    // const user = await YourUserModel.findOne({ email: normalizedUser.email });
    // if (user) {
    //     // พบผู้ใช้: อัปเดตข้อมูล (ถ้าจำเป็น) แล้วคืนค่า
    //     user.name = normalizedUser.name;
    //     await user.save();
    //     return user;
    // } else {
    //     // ไม่พบผู้ใช้: สร้างใหม่
    //     const newUser = await YourUserModel.create({
    //         sso_id: normalizedUser.id,
    //         email: normalizedUser.email,
    //         name: normalizedUser.name,
    //         role: 'member' // กำหนด Role เริ่มต้น
    //     });
    //     return newUser;
    // }

    // --- ส่วนโค้ดจำลองเพื่อการทดสอบ (ลบออกเมื่อเชื่อมต่อ DB จริง) ---
    console.log(`Simulating find/create for email: ${normalizedUser.email}`);
    return {
        id: 'mock-db-id-' + Math.random().toString(36).substring(7), // ID จากฐานข้อมูลของคุณ
        user_id: normalizedUser.id, // ID จาก SSO Provider
        email: normalizedUser.email,
        name: normalizedUser.name,
        role: 'member' // Role จากฐานข้อมูลของคุณ
    };
    // --- สิ้นสุดส่วนโค้ดจำลอง ---
}


// --- ส่วนของการสร้าง Server Express เพื่อให้ไฟล์นี้ทำงานได้ ---

const app = express();
const PORT = 3000;

// Middleware ที่จำเป็น: ทำให้ Express อ่าน JSON จาก Request Body ได้
app.use(express.json());

// นำ router ที่เราสร้างไว้มาใช้งาน
app.use('/api', router);

// Route พื้นฐานสำหรับทดสอบว่า Server ทำงาน
app.get('/', (req, res) => {
    res.send('Node.js Web App is running!');
});

app.listen(PORT, () => {
    console.log(`Server is running on http://localhost:${PORT}`);
    console.log(`SSO Handler Endpoint is available at http://localhost:${PORT}/api/sso-handler`);
});
