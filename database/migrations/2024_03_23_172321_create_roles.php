<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Créer les rôles que nous aurons dans notre application
        Role::create(['name' => 'admin']);
        Role::create(['name' => 'teacher']);
        Role::create(['name' => 'student']);
        Role::create(['name' => 'user']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Supprimer les rôles si nécessaire
        Role::where('name', 'admin')->delete();
        Role::where('name', 'teacher')->delete();
        Role::where('name', 'student')->delete();
        Role::where('name', 'user')->delete();
    }
};
