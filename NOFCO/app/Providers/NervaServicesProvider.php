<?php

namespace App\Providers;

use Illuminate\Support\Facades\App;
use Illuminate\Support\ServiceProvider;

class NervaServicesProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        App::bind('userservice', function() {
            return new \App\Services\UserService;
        });
        App::bind('menuservice', function() {
            return new \App\Services\MenuService;
        });
        App::bind('linkservice', function() {
            return new \App\Services\LinkService;
        });
        App::bind('ArticleService', function() {
            return new \App\Services\ArticleService;
        });

        App::bind('ArticleCategoryService', function() {
            return new \App\Services\ArticleCategoryService;
        });
        App::bind('ArticleCategoryTranslationService', function() {
            return new \App\Services\ArticleCategoryTranslationService;
        });
        App::bind('EventCategoryService', function() {
            return new \App\Services\EventCategoryService;
        });
        App::bind('EventCategoryTranslationService', function() {
            return new \App\Services\EventCategoryTranslationService;
        });
        App::bind('BlockCategoryService', function() {
            return new \App\Services\BlockCategoryService;
        });
        App::bind('BlockCategoryTranslationService', function() {
            return new \App\Services\BlockCategoryTranslationService;
        });
        App::bind('ProductCategoryService', function() {
            return new \App\Services\ProductCategoryService;
        });
        App::bind('ProductCategoryTranslationService', function() {
            return new \App\Services\ProductCategoryTranslationService;
        });
        App::bind('PageService', function() {
            return new \App\Services\PageService;
        });
        App::bind('PageTranslationService', function() {
            return new \App\Services\PageTranslationService;
        });
        App::bind('FileService', function() {
            return new \App\Services\FileService;
        });
        App::bind('BlockService', function() {
            return new \App\Services\BlockService;
        });
        App::bind('BlockTranslationService', function() {
            return new \App\Services\BlockTranslationService;
        });
        App::bind('EventService', function() {
            return new \App\Services\EventService;
        });
        App::bind('EventTranslationService', function() {
            return new \App\Services\EventTranslationService;
        });
        App::bind('ProductService', function() {
            return new \App\Services\ProductService;
        });
        App::bind('ProductTranslationService', function() {
            return new \App\Services\ProductTranslationService;
        });
        App::bind('CurrencyService', function() {
            return new \App\Services\CurrencyService;
        });
        App::bind('FileTranslationService', function() {
            return new \App\Services\FileTranslationService;
        });
        App::bind('BrandService', function() {
            return new \App\Services\BrandService;
        });
        App::bind('BrandTranslationService', function() {
            return new \App\Services\BrandTranslationService;
        });
        App::bind('ColorService', function() {
            return new \App\Services\ColorService;
        });
        App::bind('ColorTranslationService', function() {
            return new \App\Services\ColorTranslationService;
        });
        App::bind('ModelService', function() {
            return new \App\Services\ModelService;
        });
        App::bind('ModelTranslationService', function() {
            return new \App\Services\ModelTranslationService;
        });
        App::bind('CountryService', function() {
            return new \App\Services\CountryService;
        });
    }

    public function provides()
    {
        return [
            'UserService',
            'MenuService',
            'LinkService',
            'ArticleService',
            'ArticleCategoryService',
            'ArticleCategoryTranslationService',
            'EventCategoryService',
            'EventCategoryTranslationService',
            'BlockCategoryService',
            'BlockCategoryTranslationService',
            'ProductCategoryService',
            'ProductCategoryTranslationService',
            'PageService',
            'PageTranslationService',
            'FileService',
            'BlockService',
            'BlockTranslationService',
            'EventService',
            'EventTranslationService',
            'ProductService',
            'ProductTranslationService',
            'CurrencyService',
            'FileTranslationService',
            'BrandService',
            'BrandTranslationService',
            'ColorService',
            'ColorTranslationService',
            'ModelService',
            'ModelTranslationService',
            'CountryService',
            ];
    }
}
