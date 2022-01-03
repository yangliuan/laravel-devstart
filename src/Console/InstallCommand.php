<?php

namespace Yangliuan\LaravelDevstart\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Yangliuan\LaravelDevstart\Traits\Register;

class InstallCommand extends Command
{
    use Register;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'dev:start';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '完成一些初始化工作和安装常用工具包';


    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        if (app()->environment() === 'production') {
            return $this->error('不能在生产环境运行！');
        }

        //检测初始化脚本是否安装过
        if (Storage::disk('local')->exists('devstart.lock')) {
            return $this->error('你已经安装过了！');
        }

        //检测数据库连接是否成功
        DB::statement('SHOW TABLES');
        //发布公共文件
        system('php artisan dev:publish --force');
        //初始化app配置
        $this->regAppConfig();
        //注册appServiceProvider
        $this->regAppServiceProvider();

        if ($this->choice('是否安装IDE提示工具 barryvdh/laravel-ide-helper?', ['y', 'n'], 0) === 'y') {
            $this->info('开始安装 barryvdh/laravel-ide-helper ...');
            system('composer require barryvdh/laravel-ide-helper --dev');
            system('php artisan vendor:publish --provider="Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider" --tag=config');
            system('php artisan ide-helper:generate');
            $this->info('barryvdh/laravel-ide-helper 安装完成！');
        }

        if ($this->choice('是否安装代码风格修复工具 friendsofphp/php-cs-fixer?', ['y', 'n'], 0) === 'y') {
            $this->info('开始安装 friendsofphp/php-cs-fixer ...');
            system('composer require friendsofphp/php-cs-fixer --dev');
            system('php artisan vendor:publish --tag=php-cs-fixer --force=force');
            $this->info('friendsofphp/php-cs-fixer 安装完成！');
        }

        if ($this->choice('是否安装代码静态检测工具 phpstan/phpstan?', ['y', 'n'], 0) === 'y') {
            $this->info('开始安装 phpstan/phpstan ...');
            system('composer require phpstan/phpstan --dev');
            $this->info('phpstan/phpstan 安装完成！');
        }

        $debug = $this->choice('是否安装debug工具 ?', ['laravel/telescope', 'barryvdh/laravel-debugbar','n'], 0);
        if ($debug === 'laravel/telescope') {
            $this->info('开始安装 laravel/telescope ...');
            system('composer require laravel/telescope --dev');
            system('php artisan telescope:install');
            system('php artisan migrate');
            $this->info('laravel/telescope 安装完成！');
        }elseif($debug === 'barryvdh/laravel-debugbar'){
            $this->info('开始安装 barryvdh/laravel-debugbar ...');
            system('composer require barryvdh/laravel-debugbar --dev');
            system('php artisan vendor:publish --provider="Barryvdh\Debugbar\ServiceProvider"');
            $this->info('barryvdh/laravel-debugbar 安装完成！');
        }

        if ($this->choice('是否安装终端式代码生成器 yangliuan/generator?', ['y', 'n'], 0) === 'y') {
            $this->info('开始安装 yangliuan/generator ...');
            system('composer require "yangliuan/generator:8.*" --dev');
            $this->info('yangliuan/generator 安装完成！');
        }

        if ($this->choice('是否安装语言工具包 overtrue/laravel-lang?', ['y', 'n'], 0) === 'y') {
            $this->info('开始安装 overtrue/laravel-lang ...');
            system('composer require overtrue/laravel-lang');
            system('php artisan lang:publish zh_CN');
            $this->info('overtrue/laravel-lang 安装完成！');
        }

        if ($this->choice('是否安装模型过滤器 tucker-eric/eloquentfilter?', ['y', 'n'], 0) === 'y') {
            $this->info('开始安装 tucker-eric/eloquentfilter ...');
            system('composer require tucker-eric/eloquentfilter');
            system('php artisan vendor:publish --provider="EloquentFilter\ServiceProvider"');
            $this->regEloquentfilterConfig();
            $this->info('tucker-eric/eloquentfilter 安装完成！');
        }

        if ($this->choice('是否安装队列仪表盘 laravel/horizon?', ['y', 'n'], 0) === 'y') {
            $this->info('开始安装 laravel/horizon ...');
            system('composer require laravel/horizon');
            system('php artisan horizon:install');
            system('php artisan migrate');
            $this->info('laravel/horizon 安装完成！');
        }

        if ($this->choice('是否安装任务调度仪表盘 studio/laravel-totem?', ['y', 'n'], 0) === 'y') {
            $this->info('开始安装 studio/laravel-totem ...');
            system('composer require studio/laravel-totem');
            system('php artisan migrate');
            system('php artisan totem:assets');
            $this->info('studio/laravel-totem 安装完成！');
        }

        Storage::disk('local')->put('devstart.lock', 'installed');
    }
}
