<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class RevokeUserApiToken extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:revoke-token {user} {name?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Revoke a specific API token or all tokens for an user';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $user = User::find($this->argument('user'));

        if ($this->hasArgument('name')) {
            $tokenName = $this->argument('name');
            $user->tokens()->where('id', $tokenName)->delete();

            $this->info("Token {$tokenName} revoked for user {$user->id}");
        } else {
            $tokenCount = $user->tokens()->count();
            $user->tokens()->delete();

            $this->info("{$tokenCount} tokens revoked for user {$user->id}");
        }

    }
}
