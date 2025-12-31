<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            // foreignId: Wuxuu ku xirayaa users, cascadeOnDelete: hadii user-ka la tirtiro cinwaankuna waa tirtirmayaa
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('country');
            $table->string('district');
            $table->string('location');
            $table->string('area')->nullable();
            // Status Management: Draft waa default
            $table->enum('status', ['draft', 'submitted', 'canceled'])->default('draft')->index();
            $table->timestamps();            
            // Indexing: Waxay kordhisaa xawaaraha raadinta cinwaannada qof gaar ah
            $table->index(['user_id', 'status']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('addresses');
    }
};

