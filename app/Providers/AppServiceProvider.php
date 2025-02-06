

public function boot()
{
    // Error mesajlarını override et
    $this->loadTranslationsFrom(resource_path('lang/az'), 'messages');
}