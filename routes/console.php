<?php
use App\Models\Admin;
use Illuminate\Support\Facades\Artisan;

Artisan::command('compyvila:create-admin {email}', function () {
    $email = strtolower(trim($this->argument('email')));
    if (! filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 190) {
        $this->error('Enter a valid email address.');
        return 1;
    }
    if (Admin::where('email', $email)->exists()) {
        $this->error('That admin already exists. Existing passwords are not overwritten.');
        return 1;
    }
    $password = bin2hex(random_bytes(12));
    Admin::create(['email' => $email, 'password' => $password]);
    $this->line('Admin created: '.$email);
    $this->line('Password: '.$password);
    $this->info('Save this password in your password manager.');
    return 0;
})->purpose('Create an editorial administrator with a generated password');
