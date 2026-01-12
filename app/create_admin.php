<?php
// create_admin.php - Run this once to create admin user

require_once __DIR__ . '/vendor/autoload.php';

use App\Config;
use PDO;
use App\Models\UserModel;

try {
    // Connect to database
    $pdo = new PDO(
        Config::getDsn(),
        Config::DB_USER,
        Config::DB_PASSWORD
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "🔧 Setting up RoomShift Admin User...\n\n";
    
    // Create admin user model
    $admin = new UserModel([
        'email' => 'admin@roomshift.com',
        'name' => 'Administrator',
        'role' => 'admin'
    ]);
    
    // Hash the password
    $admin->setPassword('admin123');
    
    // Check if admin already exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email");
    $stmt->execute([':email' => 'admin@roomshift.com']);
    
    if ($stmt->fetch()) {
        echo "⚠️  Admin user already exists. Updating password...\n";
        
        // Update existing admin
        $updateStmt = $pdo->prepare("UPDATE users SET password_hash = :password WHERE email = :email");
        $updateStmt->execute([
            ':password' => $admin->password_hash,
            ':email' => 'admin->email'
        ]);
        
        echo "✅ Admin password updated!\n";
    } else {
        // Insert new admin
        $insertStmt = $pdo->prepare("
            INSERT INTO users (email, password_hash, name, role, created_at, updated_at) 
            VALUES (:email, :password, :name, :role, NOW(), NOW())
        ");
        
        $insertStmt->execute([
            ':email' => $admin->email,
            ':password' => $admin->password_hash,
            ':name' => $admin->name,
            ':role' => $admin->role
        ]);
        
        echo "✅ Admin user created successfully!\n";
    }
    
    echo "\n📋 Login Credentials:\n";
    echo "-------------------\n";
    echo "Email: admin@roomshift.com\n";
    echo "Password: admin123\n";
    echo "Role: Admin\n";
    echo "-------------------\n\n";
    echo "🌐 You can now login at: http://localhost/login\n";
    
} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "\n💡 Troubleshooting:\n";
    echo "1. Make sure database is running\n";
    echo "2. Check Config.php credentials\n";
    echo "3. Verify users table exists\n";
}