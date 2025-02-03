# API Documentation

## BaseController

BaseController digər API kontrollerlər üçün baza sinif rolunu oynayır və ümumi cavab formatını təmin edir.

### Metodlar

#### `sendResponse(mixed $result, string $message = '', int $code = 200): JsonResponse`

Uğurlu cavabları qaytarmaq üçün istifadə olunur.

- `$result` (qarışıq): Cavabda qaytarılacaq məlumat
- `$message` (string, default: ''): Cavaba əlavə olunacaq mesaj
- `$code` (int, default: 200): HTTP status kodu

#### `sendError(string $error, array $errorMessages = [], int $code = 404): JsonResponse`

Xəta cavablarını qaytarmaq üçün istifadə olunur.

- `$error` (string): Xəta mesajı
- `$errorMessages` (array, default: []): Əlavə xəta mesajları
- `$code` (int, default: 404): HTTP status kodu

## AuthController

AuthController istifadəçi autentifikasiyası və qeydiyyatı ilə bağlı API nöqtələrini idarə edir.

### Endpoints

#### `POST /api/v1/auth/login`

İstifadəçi girişini həyata keçirir.

- Request: `LoginRequest`
- Response: İstifadəçi məlumatları və token

#### `POST /api/v1/auth/register`

Yeni istifadəçi qeydiyyatını həyata keçirir.

- Request: `RegisterRequest`
- Response: Yeni yaradılmış istifadəçi məlumatları və token

#### `POST /api/v1/auth/logout`

İstifadəçi çıxışını həyata keçirir.

- Response: Uğurlu çıxış mesajı

#### `GET /api/v1/auth/user`

Cari autentifikasiya olunmuş istifadəçinin məlumatlarını qaytarır.

- Response: İstifadəçi məlumatları

## RegionController

RegionController region əməliyyatlarını idarə edir.

### Endpoints

#### `GET /api/v1/regions`

Bütün regionları siyahı şəklində qaytarır.

- Response: Regionlar siyahısı

#### `POST /api/v1/regions`

Yeni region yaradır.

- Request: `StoreRegionRequest`
- Response: Yaradılan region

#### `GET /api/v1/regions/{id}`

Verilmiş ID-yə uyğun regionu qaytarır.

- Response: Region məlumatları

#### `PUT /api/v1/regions/{id}`

Mövcud regionu yeniləyir.

- Request: `UpdateRegionRequest`
- Response: Yenilənmiş region

#### `DELETE /api/v1/regions/{id}`

Verilmiş ID-yə uyğun regionu silir.

- Response: Uğurlu silmə mesajı

## SectorController

SectorController sektor əməliyyatlarını idarə edir.

### Endpoints

#### `GET /api/v1/sectors`

Bütün sektorları siyahı şəklində qaytarır. `region_id` query parametri ilə regionlara görə filtirləmə aparıla bilər.

- Response: Sektorlar siyahısı

#### `POST /api/v1/sectors`

Yeni sektor yaradır.

- Request: `StoreSectorRequest`
- Response: Yaradılan sektor

#### `GET /api/v1/sectors/{id}`

Verilmiş ID-yə uyğun sektoru qaytarır.

- Response: Sektor məlumatları

#### `PUT /api/v1/sectors/{id}`

Mövcud sektoru yeniləyir.

- Request: `UpdateSectorRequest`
- Response: Yenilənmiş sektor

#### `DELETE /api/v1/sectors/{id}`

Verilmiş ID-yə uyğun sektoru silir.

- Response: Uğurlu silmə mesajı

## SchoolController

SchoolController məktəb əməliyyatlarını idarə edir.

### Endpoints

#### `GET /api/v1/schools`

Bütün məktəbləri siyahı şəklində qaytarır. `sector_id` query parametri ilə sektorlara görə filtirləmə aparıla bilər.

- Response: Məktəblər siyahısı

#### `POST /api/v1/schools`

Yeni məktəb yaradır.

- Request: `StoreSchoolRequest`
- Response: Yaradılan məktəb

#### `GET /api/v1/schools/{id}`

Verilmiş ID-yə uyğun məktəbi qaytarır.

- Response: Məktəb məlumatları

#### `PUT /api/v1/schools/{id}`

Mövcud məktəbi yeniləyir.

- Request: `UpdateSchoolRequest`
- Response: Yenilənmiş məktəb

#### `DELETE /api/v1/schools/{id}`

Verilmiş ID-yə uyğun məktəbi silir.

- Response: Uğurlu silmə mesajı

#### `GET /api/v1/schools/{id}/admins`

Verilmiş ID-yə uyğun məktəbdəki adminlərin siyahısını qaytarır.

- Response: Məktəb adminlərinin siyahısı
