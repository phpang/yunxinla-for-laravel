<?php

namespace Phpang\Yunxinlaclient;

use Illuminate\Support\ServiceProvider;

class YunxinlaclientServiceProvider extends ServiceProvider
{
    /**
    * 服务提供者加是否延迟加载.
    *
    * @var bool
    */
   protected $defer = true; // 延迟加载服务
   /**
    * Bootstrap the application services.
    *
    * @return void
    */
   public function boot()
   {
       $this->loadViewsFrom(__DIR__ . '/views', 'Packagetest'); // 视图目录指定
       $this->publishes([
           __DIR__.'/views' => base_path('resources/views/vendor/packagetest'),  // 发布视图目录到resources 下
           __DIR__.'/config/packagetest.php' => config_path('packagetest.php'), // 发布配置文件到 laravel 的config 下
       ]);
   }
   /**
    * Register the application services.
    *
    * @return void
    */
   public function register()
   {
      $this->app->singleton('Yunxinlaclient', function ($app) {
          // dd($app->config->get('yunxin'));
          
          return new Yunxinlaclient($app->config->get('yunxin'));
      });
       //  // 单例绑定服务
       // $this->app->singleton('packagetest', function ($app) {
       //     return new Packagetest($app['session'], $app['config']);
       // });
   }
   /**
    * Get the services provided by the provider.
    *
    * @return array
    */
   public function provides()
   {
       // 因为延迟加载 所以要定义 provides 函数 具体参考laravel 文档
       return ['Yunxinlaclient'];
   }
}
