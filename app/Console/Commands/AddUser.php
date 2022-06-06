<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class AddUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:add';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add an administrative user';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $name = $this->ask("User's name");
        $email = $this->ask("User's email");
        $password = $this->secret("Choose a password");
        $user = User::forceCreate([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'role' => User::ROLE_ADMIN,
        ]);
        $this->info('Created user with ID ' . $user->id);
        return 0;
    }
}
