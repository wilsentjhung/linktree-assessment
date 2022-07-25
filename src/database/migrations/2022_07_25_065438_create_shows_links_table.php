<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShowsLinksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     * @TODO Add more cols related to shows base link (if any)
     */
    public function up()
    {
        Schema::create('shows_links', function (Blueprint $table) {
            $table->id();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shows_links');
    }
}
