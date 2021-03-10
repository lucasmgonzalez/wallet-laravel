<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class ShowUserBalance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:balance {user?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Show an user\'s balance or all users balance if no user is defined';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    protected function showUserBalance(User $user)
    {
        $this->info("User {$user->id} has {$user->balance()->money}");
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $user_id = $this->argument('user') ?? null;

        if (is_null($user_id)) {
            $users = User::all();

            foreach ($users as $user) {
                $this->showUserBalance($user);
            }
        } else {
            $user = User::find($user_id);

            $this->showUserBalance($user);
        }
    }
}
