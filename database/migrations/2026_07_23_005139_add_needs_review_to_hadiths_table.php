<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('hadiths', function (Blueprint $table) {
            $table->boolean('needs_review')->default(false)->after('grade_source');
        });
    }

    public function down()
    {
        Schema::table('hadiths', function (Blueprint $table) {
            $table->dropColumn('needs_review');
        });
    }
};
