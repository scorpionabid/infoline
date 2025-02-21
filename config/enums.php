<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Məktəb Tipləri
    |--------------------------------------------------------------------------
    */
    'school_types' => [
        'primary' => 'İbtidai məktəb',
        'secondary' => 'Orta məktəb',
        'high' => 'Tam orta məktəb',
        'lyceum' => 'Lisey',
        'gymnasium' => 'Gimnaziya',
        'vocational' => 'Peşə məktəbi',
        'special' => 'Xüsusi məktəb',
        'evening' => 'Axşam məktəbi',
        'boarding' => 'İnternat məktəbi'
    ],

    /*
    |--------------------------------------------------------------------------
    | İstifadəçi Tipləri
    |--------------------------------------------------------------------------
    */
    'user_types' => [
        'super_admin' => 'Super Admin',
        'admin' => 'Admin',
        'region_admin' => 'Region Admini',
        'sector_admin' => 'Sektor Admini',
        'school_admin' => 'Məktəb Admini',
        'teacher' => 'Müəllim',
        'student' => 'Şagird'
    ],

    /*
    |--------------------------------------------------------------------------
    | Məlumat Kateqoriyaları
    |--------------------------------------------------------------------------
    */
    'data_categories' => [
        'general' => 'Ümumi məlumatlar',
        'infrastructure' => 'İnfrastruktur',
        'staff' => 'İşçi heyəti',
        'students' => 'Şagirdlər',
        'academic' => 'Akademik göstəricilər',
        'financial' => 'Maliyyə',
        'inventory' => 'İnventar',
        'activities' => 'Fəaliyyətlər'
    ],

    /*
    |--------------------------------------------------------------------------
    | Status Növləri
    |--------------------------------------------------------------------------
    */
    'status_types' => [
        'active' => 'Aktiv',
        'inactive' => 'Deaktiv',
        'pending' => 'Gözləmədə',
        'suspended' => 'Dayandırılıb',
        'archived' => 'Arxivləşdirilib'
    ],

    /*
    |--------------------------------------------------------------------------
    | Bildiriş Növləri
    |--------------------------------------------------------------------------
    */
    'notification_types' => [
        'info' => 'Məlumat',
        'warning' => 'Xəbərdarlıq',
        'success' => 'Uğurlu',
        'error' => 'Xəta',
        'deadline' => 'Son tarix',
        'task' => 'Tapşırıq',
        'message' => 'Mesaj'
    ],

    /*
    |--------------------------------------------------------------------------
    | Hesabat Növləri
    |--------------------------------------------------------------------------
    */
    'report_types' => [
        'daily' => 'Gündəlik',
        'weekly' => 'Həftəlik',
        'monthly' => 'Aylıq',
        'quarterly' => 'Rüblük',
        'yearly' => 'İllik',
        'custom' => 'Xüsusi'
    ]
];