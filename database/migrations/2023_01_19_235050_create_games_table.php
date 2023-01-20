<?php

use App\Models\Competition;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Query\Expression;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('games', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('competition')->constrained('competitions');
            $table->foreignId('team_a')->constrained('teams');
            $table->string('team_a_score', 2)->default('');
            $table->foreignId('team_b')->constrained('teams');
            $table->string('team_b_score', 2)->default('');
            $table->dateTime('datetime')->default(new Expression('NOW()'));
            $table->string('stadium', 50);
            $table->enum('status', ['Pendiente', 'Finalizado'])->default('Pendiente');
            $table->string('description', 100)->default('');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('games');
    }
};
