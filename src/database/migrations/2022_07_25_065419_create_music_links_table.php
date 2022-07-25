<?php

use App\Models\MusicLink;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMusicLinksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     * @TODO Add more cols related to music base link (if any)
     */
    public function up()
    {
        Schema::create('music_links', function (Blueprint $table) {
            $table->id();
            $table->string('title', MusicLink::LEN_TITLE)->index();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('music_links');
    }
}
