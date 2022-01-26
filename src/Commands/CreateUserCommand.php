<?php

namespace Nonoesp\Folio\Commands;

use Illuminate\Console\Command;
use InvalidArgumentException;

class CreateUserCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'folio:user {email} {password} {name?} {--admin}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a user.';

    /**
     * Execute the console command.
     *
     * @return void
     *
     * @throws \InvalidArgumentException
     */
    public function handle()
    {

        if (
            $this->argument('email') &&
            $this->argument('password')
        ) {

            $name = $this->argument('name');
            $email = $this->argument('email');
            $password = $this->argument('password');
            $admin = $this->option('admin');

            if (\App\User::where('email', $email)->count() > 0) {
                $this->error("A user with $email already exists");
                return;
            }

            $user = $this->createUser(
                $name ? $name : '',
                $email,
                $password,
                $admin
            );

            $this->comment($user);
            $this->info('User was create successfully.');

        } else {
            $this->error('Please provide a name, email, and password for the new user.');
        }
    }

    protected function createUser($name ,$email, $password, $admin) {

        $user = \App\User::create([
            'name' => $name,
            'email' => $email,
            'password' => \Hash::make($password),
        ]);

        $user->is_admin = $admin;
        $user->save();

        return $user;
    }

}