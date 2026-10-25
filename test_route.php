<?php
require_once 'vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Test route resolution
echo "Testing route resolution:\n";
echo "Route exists: " . (Route::has('formasi.create') ? 'YES' : 'NO') . "\n";

try {
    $url = route('formasi.create');
    echo "Route URL: $url\n";
} catch (Exception $e) {
    echo "Error generating route: " . $e->getMessage() . "\n";
}

// Test admin user exists
$adminCount = \App\Models\User::where('role', 'admin')->count();
echo "Admin users count: $adminCount\n";

if ($adminCount > 0) {
    $admin = \App\Models\User::where('role', 'admin')->first();
    echo "Admin email: " . $admin->email . "\n";
    echo "Admin role: " . $admin->role . "\n";
    echo "isAdmin(): " . ($admin->isAdmin() ? 'true' : 'false') . "\n";
}