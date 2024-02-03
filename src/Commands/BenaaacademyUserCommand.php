<?php

namespace Benaaacademy\Platform\Commands;

use Benaaacademy\Platform\Command;
use Benaaacademy\Roles\Models\Role;
use Benaaacademy\Users\Models\User;

/*
 * Class BenaaacademyUserCommand
 */
class BenaaacademyUserCommand extends Command
{

    /*
     * @var string
     */
    protected $name = 'Benaaacademy:user';

    /*
     * @var string
     */
    protected $description = "Create a new user";


    /*
     * @return bool
     */
    public function handle()
    {

        $user = new User();

        $this->info("\n");

        $this->info("Creating User account:");

        $username = $this->ask("Username");
        $password = $this->secret("Password");
        $email = $this->ask("Email");
        $name = $this->ask("Full name");

        $user->username = $username;
        $user->password = $password;
        $user->email = $email;
        $user->repassword = $password;
        $user->first_name = $name;
        $user->api_token = $user->newApiToken();

        if (!$user->validate()) {

            foreach ($user->errors()->all() as $error) {
                $this->error($error);
            }

            if (count($user->errors()->all())) {
                return $this->handle();
            }
        }

        $user->root = 1;
        $user->role_id = Role::superadmin()->first()->id;
        $user->backend = 1;
        $user->status = 1;

        $user->save();

        return $this->info("User created successfully");

    }

    function getRoleID()
    {

        $this->info("System roles:");

        $roles = Role::all();

        $index = 1;

        foreach ($roles as $role) {
            $this->info("[$index] $role->name");
            $index++;
        }

        $index = $this->ask("Select role for this user");

        if (isset($roles[$index - 1])) {
            return $roles[$index - 1]->id;
        }

        $this->error("Invalid user role");

        $this->info("\n");

        return $this->getRoleID();
    }

}
