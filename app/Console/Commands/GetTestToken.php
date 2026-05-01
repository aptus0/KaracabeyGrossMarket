<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\Auth\ApiTokenIssuer;
use Illuminate\Console\Command;

class GetTestToken extends Command
{
    protected $signature = 'test:token {--user-id=3}';
    protected $description = 'Get or create API token for testing';

    public function handle(): int
    {
        $userId = $this->option('user-id');
        $user = User::find($userId);

        if (!$user) {
            $this->error("User {$userId} not found");
            return 1;
        }

        $issuer = app(ApiTokenIssuer::class);
        $result = $issuer->issue($user, 'cli-test');

        $this->info("API Token for {$user->name}:");
        $this->line($result['token']);

        return 0;
    }
}
