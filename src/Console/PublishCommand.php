<?php

namespace Yangliuan\LaravelDevstart\Console;

use Illuminate\Console\Command;

class PublishCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dev:publish {--force : Overwrite any existing files}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publish all of the Telescope resources';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {

    }
}
