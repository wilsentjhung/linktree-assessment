<?php

use App\Models\ShowsSublink;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShowsSublinksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shows_sublinks', function (Blueprint $table) {
            $table->id();
            $table->string('name')->index();
            $table->string('url');
            $table->enum('status', ShowsSublink::STATUS_LIST)->default(ShowsSublink::STATUS_NOT_ON_SALE)->index();
            $table->date('date')->nullable()->index();
            $table->string('venue')->index();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shows_sublinks');
    }
}
