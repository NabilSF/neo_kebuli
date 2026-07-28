<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$emails = ['superadmin@neokbli.local', 'admin@neokbli.local', 'operator@neokbli.local'];

foreach ($emails as $email) {
    $u = \App\Models\User::where('email', $email)->first();
    echo "\n--- $email ---\n";
    if ($u) {
        echo "Roles (relation): " . json_encode($u->roles->pluck('name')) . "\n";
        echo "getRoleNames: " . json_encode($u->getRoleNames()) . "\n";
        echo "hasRole super-admin: " . ($u->hasRole('super-admin') ? 'Yes' : 'No') . "\n";
        echo "hasRole admin: " . ($u->hasRole('admin') ? 'Yes' : 'No') . "\n";
        echo "hasRole operator: " . ($u->hasRole('operator') ? 'Yes' : 'No') . "\n";
    } else {
        echo "User not found\n";
    }
}
