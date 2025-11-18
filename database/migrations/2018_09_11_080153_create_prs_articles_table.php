<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->integer('price')->nullable();
            $table->text('abstract');
            $table->text('excerpt');
            $table->string('submitted_document');
            $table->string('publish_document')->nullable();
            $table->enum('status', ['articles_under_review', 'accepted_articles', 'major_revisions', 'minor_revisions', 'rejected'])->default('articles_under_review');
            $table->string('editor_comments')->nullable();
            $table->unsignedBigInteger('corresponding_author_id');
            $table->tinyInteger('notify')->default('0');
            $table->unsignedBigInteger('article_category_id')->nullable();
            $table->unsignedBigInteger('edition_id')->nullable();
            $table->string('unique_code');
            $table->tinyInteger('author_notify')->default('0');
            $table->integer('hits')->default('0');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};

