# İnfoLine - Məktəb Məlumatları Toplama Sistemi
## Quraşdırma və İstifadə Təlimatları

### Mündəricat
1. [Sistem Tələbləri](#sistem-tələbləri)
2. [Quraşdırma](#quraşdırma)
3. [Konfiqurasiya](#konfiqurasiya)
4. [İstifadə Təlimatları](#istifadə-təlimatları)
5. [Təhlükəsizlik](#təhlükəsizlik)
6. [Tez-tez Verilən Suallar](#tez-tez-verilən-suallar)

### Sistem Tələbləri
- Web Server: Nginx 1.18+
- PHP 8.1+
- MySQL 8.0+
- Composer
- Node.js (WebSocket server üçün)
- SSL Sertifikatı

### Quraşdırma

#### 1. Layihəni Klonlama
```bash
git clone [repository_url]
cd infoline_app

composer install
npm install # (WebSocket server üçün)

# .env faylını yaratmaq
cp .env.example .env

# Verilənlər bazası parametrlərini .env faylında konfiqurasiya etmək
# Verilənlər bazası miqrasiyalarını işə salmaq
php scripts/migrate.php

# Web Server konfiqurasiyası (Nginx nümunəsi config/ qovluğunda)
# WebSocket serverin işə salınması
node websocket/server.js

# Storage qovluğuna yazma ica  zələrinin verilməsi
chmod -R 775 storage/

DB_HOST=localhost
DB_NAME=infoline_db
DB_USER=your_user
DB_PASS=your_password

WEBSOCKET_HOST=localhost
WEBSOCKET_PORT=8080

APP_DEBUG=false
APP_URL=https://your-domain.com

İstifadə Təlimatları
SuperAdmin İstifadəçisi üçün
Sistemə Giriş
Brauzerinizdə https://your-domain.com/login səhifəsinə daxil olun
SuperAdmin kredensialları ilə daxil olun
Yeni Sütun Yaratma
Dashboard-da "+" düyməsini klikləyin
Sütun adı və tipini daxil edin
Son tarix təyin edin (istəyə bağlı)
"Yadda saxla" düyməsini klikləyin
Məlumatların İxracı
"İxrac" düyməsini klikləyin
Excel formatını seçin
Tarix aralığını və məktəb(lər)i seçin
"İxrac et" düyməsini klikləyin
Məktəb Admini üçün
Sistemə Giriş
Brauzerinizdə https://your-domain.com/login səhifəsinə daxil olun
Məktəb admin kredensialları ilə daxil olun
Məlumat Daxil Etmə
Tələb olunan sütunu tapın
Məlumatı daxil edin
"Yadda saxla" düyməsini klikləyin
Təhlükəsizlik
Şifrə Təhlükəsizliyi
Minimum 8 simvol
Ən azı 1 böyük hərf
Ən azı 1 rəqəm
Ən azı 1 xüsusi simvol
İki Faktorlu Autentifikasiya
SMS və ya email vasitəsilə təsdiq kodu
Tez-tez Verilən Suallar
Şifrəmi unutmuşam. Nə etməliyəm?
"Şifrəni unutdum" linkini klikləyin
Email ünvanınızı daxil edin
Göndərilən təlimatları izləyin
Real-time yeniləmələr işləmir
WebSocket serverin işlək olduğunu yoxlayın
Brauzerinizi yeniləyin
Cache və cookie-ləri təmizləyin
Texniki Dəstək
Texniki problemlər üçün:

Email: support@infoline.edu.az
Tel: [dəstək nömrəsi]


Bu təlimat faylı sisteminizin quraşdırılması və istifadəsi üçün ətraflı məlumat təmin edir. Təlimatı öz ehtiyaclarınıza uyğun olaraq düzəldə və ya genişləndirə bilərsiniz. Xüsusilə:

1. Repository URL-ni
2. Dəstək əlaqə məlumatlarını
3. Lisenziya məlumatlarını
4. Spesifik domain adlarını

öz məlumatlarınızla əvəz etməyi unutmayın.