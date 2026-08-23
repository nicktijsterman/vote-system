<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ManageUserCommand extends Command
{
    final public const string DEFAULT_ADMIN_NAME = 'admin';
    final public const string DEFAULT_ADMIN_PASSWORD = 'password';
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'votesystem:admin';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update or create the admin user of the vote system application';

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
     */
    public function handle(): int
    {
        $name = config('vote-system.admin_name') ?: self::DEFAULT_ADMIN_NAME;
        $password =
            config('vote-system.admin_password') ?:
            self::DEFAULT_ADMIN_PASSWORD;

        // Deployments driven by .docker/entrypoint.sh never reach this branch: the
        // entrypoint generates and persists VS_ADMIN_PASSWORD into .env before this
        // command runs. This is a safety net for non-Docker/bare-metal deployments
        // that call this command directly without ever setting VS_ADMIN_PASSWORD.
        if (
            config('app.env') === 'production' &&
            $password === self::DEFAULT_ADMIN_PASSWORD
        ) {
            $password = Str::random(20);
            $this->warn(
                "No admin password was configured (VS_ADMIN_PASSWORD). Generated one instead of using the insecure default:\n{$password}\n" .
                'This is NOT persisted - set VS_ADMIN_PASSWORD in your environment or it will change on every run of this command.'
            );
        }

        $user = User::updateOrCreate(
            ['name' => $name],
            ['password' => Hash::make($password)]
        );
        $this->info(
            'Created or updated the admin user with name: ' . $user->name
        );

        return 0;
    }
}
