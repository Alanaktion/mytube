<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

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
        $name = $this->ask("User's name");
        $email = $this->ask("User's email");
        $password = $this->secret("Choose a password");
        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => $password,
        ]);
        $this->info('Created user with ID ' . $user->id);
        return 0;
    }
}
