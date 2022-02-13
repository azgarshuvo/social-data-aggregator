<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSocialPostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('social_posts', function (Blueprint $table) {
            $table->id();
            $table->string('data_from');
            $table->string('post_id')->nullable();
            $table->mediumText('serach_key');
            $table->text('post_details');
            $table->string('like_count')->nullable();
            $table->string('comment_count')->nullable();
            $table->string('author_name')->nullable();
            $table->string('author_bio')->nullable();
            $table->string('author_location')->nullable();
            $table->string('post_date')->nullable();
            $table->string('language')->nullable();
            $table->mediumText('post_url')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('social_posts');
    }
}
