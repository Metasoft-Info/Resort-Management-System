<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            // SQLite doesn't support MODIFY COLUMN; recreate table
            $users = DB::table('users')->get();
            Schema::drop('users');
            Schema::create('users', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('email')->unique();
                $table->timestamp('email_verified_at')->nullable();
                $table->string('password');
                $table->string('role', 50)->default('staff');
                $table->rememberToken();
                $table->timestamps();
            });
            foreach ($users as $user) {
                DB::table('users')->insert((array) $user);
            }
        } else {
            // Change ENUM to VARCHAR to support all role types
            DB::statement("ALTER TABLE users MODIFY COLUMN role VARCHAR(50) NOT NULL DEFAULT 'staff'");
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('owner', 'staff') DEFAULT 'staff'");
        }
    }
};
