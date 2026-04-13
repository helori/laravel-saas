<?php

// This migration is intentionally empty.
// The team/user relationship is a direct foreign key (users.team_id + users.role)
// rather than a many-to-many pivot table. No team_user table is needed.

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up() {}
    public function down() {}
};
