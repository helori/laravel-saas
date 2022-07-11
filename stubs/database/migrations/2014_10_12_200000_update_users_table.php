<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('firstname')->nullable()->default(null);
            $table->string('lastname')->nullable()->default(null);
            $table->string('phone')->nullable()->default(null);
            $table->boolean('is_root')->nullable()->default(false);
            $table->foreignId('current_team_id')->nullable()->index();
            $table->dropColumn([
                'name',
            ]);
            $table->boolean('activated')->nullable()->default(true);
            $table->timestamp('invited_at')->nullable()->default(null);
            $table->string('invited_to')->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('name')->nullable()->default(null);
            $table->dropColumn([
                'firstname',
                'lastname',
                'phone',
                'is_root',
                'current_team_id',
            ]);
        });
    }
}
