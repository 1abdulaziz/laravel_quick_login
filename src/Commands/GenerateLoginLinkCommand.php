<?php

namespace LaravelQuickLogin\Commands;

use Illuminate\Console\Command;
use LaravelQuickLogin\OneTimeLoginService;

class GenerateLoginLinkCommand extends Command
{
    protected $signature = 'uli {userId} {--minutes=2}';
    protected $description = 'Generate a one-time login link for a user';

    public function handle(OneTimeLoginService $service)
    {
        $userId = $this->argument('userId');
        $minutes = $this->option('minutes');

        try {
            $url = $service->generateLoginUrl($userId, $minutes);
            $this->info("One-time login URL: " . $url);
        } catch (\InvalidArgumentException $e) {
            $this->error($e->getMessage());
        }
    }
}
