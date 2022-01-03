<?php

namespace Yangliuan\LaravelDevstart;

class DevstartServiceProvider extends \Illuminate\Support\ServiceProvider
{
    public function boot()
    {
        $this->registerCommands();
        $this->registerPublishing();
    }

    /**
     * Register the package's publishable resources.
     *
     * @return void
     */
    private function registerPublishing()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([ __DIR__ . '/../.php-cs-fixer.php' => base_path('.php-cs-fixer.php') ], 'php-cs-fixer');
        }
    }

    /**
     * Register the package's commands.
     *
     * @return void
     */
    protected function registerCommands()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                Console\InstallCommand::class,
                Console\ResetCommand::class,
                Console\PublishCommand::class,
            ]);
        }
    }
}
