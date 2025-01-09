<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use App\Models\Category;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Blade::directive('removeImageLinks', function ($expression) {
            return "<?php echo preg_replace('/<a[^>]*>(<img[^>]*>)<\/a>/i', '$1', $expression); ?>";
        });

        View::composer('components.header', function ($view) {
            $view->with('categories', Category::all());
        });
    }
}
