<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('restriction_settings', function (Blueprint $table) {
            $table->id();
            $table->string('restriction_name')->unique();
            $table->boolean('is_restriction_enabled')->default(false);
            $table->string('image')->nullable();
            $table->string('title')->nullable();
            $table->string('sub_title')->nullable();
            $table->string('url_label')->nullable();
            $table->string('redirection_url')->nullable();
            $table->boolean('is_button_enabled')->default(false);
            $table->timestamps();
        });

        DB::table('restriction_settings')->insert([
            'restriction_name' => 'Maintenance Mode',
            'is_restriction_enabled' => false,
            'image' => 'Restriction/Maintenance_Mode.png',
            'title' => 'App Under Maintenance',
            'sub_title' => 'Our app is currently undergoing scheduled maintenance to improve our services. We should be back shortly. For urgent matters, please Contact Us.',
            'url_label' => 'Contact Us',
            'redirection_url' => 'tel:+10000000000',
            'is_button_enabled' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('restriction_settings');
    }
};
