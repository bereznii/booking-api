<?php

namespace App\Console\Commands;

use Illuminate\Support\Str;
use Illuminate\Console\Command;

/**
 * Class KeyGenerateCommand
 * @package App\Console\Commands
 */
class KeyGenerateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'key:generate {--show : Display the key instead of modifying file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Set the application key";

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $key = $this->getRandomKey();
        $appKey = 'base64:' . $key;

        if ($this->option('show')) {
            $this->line('<comment>' . $key . '</comment>');
            return;
        }

        $path = base_path('.env');

        if (file_exists($path)) {

            $fileContent = file_get_contents($path);

            if ($fileContent !== false) {
                file_put_contents(
                    $path,
                    preg_replace('/(APP_KEY=)(\s|.*)\n/', ("APP_KEY={$appKey}\n"), $fileContent)
                );

                $this->info("Application key [$key] set successfully.");
            }

        } else {
            $this->info("File does not exist. Application key was not set.");
        }
    }

    /**
     * Generate a random key for the application.
     *
     * @return string
     */
    protected function getRandomKey(): string
    {
        return Str::random(64);
    }
}
