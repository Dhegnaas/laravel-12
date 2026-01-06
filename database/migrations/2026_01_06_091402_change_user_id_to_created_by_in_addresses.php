<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
    Schema::table('addresses', function (Blueprint $table) {
        // 1. Marka hore tirtir Foreign Key-ga (Constraint)
        // Laravel badanaa wuxuu u bixiyaa 'table_column_foreign'
        $table->dropForeign(['user_id']); 
        
        // 2. Hadda tirtir Index-ka (mar haddii foreign key-gii meesha ka baxay)
        $table->dropIndex(['user_id', 'status']);

        // 3. Beddel magaca column-ka
        $table->renameColumn('user_id', 'created_by');
    });

    Schema::table('addresses', function (Blueprint $table) {
        // 4. Ku dar Foreign Key-ga cusub iyo Index-ka cusub
        $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
        $table->index(['created_by', 'status']);
    });
}

public function down(): void {
    Schema::table('addresses', function (Blueprint $table) {
        $table->dropForeign(['created_by']);
        $table->dropIndex(['created_by', 'status']);
        
        $table->renameColumn('created_by', 'user_id');
    });

    Schema::table('addresses', function (Blueprint $table) {
        $table->foreignId('user_id')->constrained()->cascadeOnDelete();
        $table->index(['user_id', 'status']);
    });
}};