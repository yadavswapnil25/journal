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
        Schema::create('email_templates', function (Blueprint $table) {
            $table->id();
            $table->string('subject');
            $table->string('title');
            $table->unsignedBigInteger('role_id')->nullable();
            $table->enum('email_type', ['new_article', 'assign_reviewer', 'resubmit_article', 'reviewer_feedback', 'accepted_articles_editor_feedback','minor_revisions_editor_feedback','major_revisions_editor_feedback','rejected_editor_feedback', 'publish_edition', 'new_user', 'change_password', 'update_user', 'new_order', 'success_order', 'cancel_order']);
            $table->text('body');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_templates');
    }
};

