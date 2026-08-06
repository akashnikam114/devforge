<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateBusinessSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('business_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        DB::table('business_settings')->insert([
            [
                'key' => 'app_name',
                'value' => '__PROJECT_NAME__',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'app_logo',
                'value' => 'app_logo.png',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'app_email',
                'value' => 'support@example.com',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'app_phone',
                'value' => '+1 000 000 0000',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'currency_symbol',
                'value' => 'USD',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'admin_maintenance_mode',
                'value' => 'false',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'firebase_project_id',
                'value' => '__PROJECT_SLUG__',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'firebase_api_key',
                'value' => 'your-firebase-api-key',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'encryption_key',
                'value' => 'secure-encryption-key-here',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'privacy_policy',
                'value' => 'Update your privacy policy content from the admin panel.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'terms_and_conditions',
                'value' => 'Update your terms and conditions content from the admin panel.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('business_settings');
    }
}
