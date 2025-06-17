<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('layouts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->foreignId('header_id')->nullable()->constrained('headers')->onDelete('set null'); 
            $table->foreignId('footer_id')->nullable()->constrained('footers')->onDelete('set null'); 
            $table->timestamps();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('layouts');
    }
};
