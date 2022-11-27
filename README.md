# BotAdminPanel
Telegram Bot uchun qulay admin panel.
# Imkoniyatlar
![](https://komiljonovdev.uz/okdeveloper/tgbots/Bot%20And%20Details/assets/screens/admin.jpg)
![](https://komiljonovdev.uz/okdeveloper/tgbots/Bot%20And%20Details/assets/screens/addAdmin.jpg)
![](https://komiljonovdev.uz/okdeveloper/tgbots/Bot%20And%20Details/assets/screens/deleteAdmin.jpg)
![](https://komiljonovdev.uz/okdeveloper/tgbots/Bot%20And%20Details/assets/screens/channel.jpg)
![](https://komiljonovdev.uz/okdeveloper/tgbots/Bot%20And%20Details/assets/screens/ads.jpg)
- Botga admin qo'shish, o'chirish
- Majburiy a'zolik uchun kanal qo'shish, o'chirish
- Majburiy a'zolik On va Off rejimga o'tkazish
- Reklama yuborish, bot 3 ta tilda ishlashi ham hisobga olingan, reklamani foydalanuvchi tiliga qarab fodalanuvchi va guruhlarga yuborish
- Reklama yuborish cron qilib sekinlatilgan holatda aniq ishlaydi
# O'rnatish va ishatish
1. Barcha fayllarni serverga ko'chiriladi
2. `index.php` faylidagi API_KEY o'zgarmasga va `config/config.php` faylidagi `bot` funksiyasiga Bot token kiritiladi
3. `config/dbConfig.php` fayliga baza ma'lumotlari kiritiladi
4. `bot.sql` fayliga ma'lumotlar bazasi nomi yoziladi
5. `bot.sql` fayli mysql bazaga yuklanadi
6. `config/json/admins.json` fayliga admin ID yoziladi
7. Bot 3 ta tilda ishlashi uchun `config/json/lang.json` fayliga har bir habar uchun 3 ta tilda matnlar yoziladi
8. Har qanday habarlarga javob berish uchun, $update->message null bo'lmagan va channel($fromid) funksiyasidan keyin yoziladi
9. Har qanday callback_query ga javob berish uchun, $update->callback_query null bo'lmagan va channel($cbid) funksiyasidan keyin yoziladi
10. Qolgan update larga ham shu shablonda javob beriladi
11. Bot turgan asosiy `index.php` fayli cron va webhook qilinadi
12. `https://yourdomain.com./path/index.php?sendMessage=true` kabi reklama yuborish tezligiga ko'ra, ixtiyoriy vaqt oralig'ida cron qilinadi
