<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChatResponsesTable extends Migration
{
    public function up()
    {
        Schema::create('chat_responses', function (Blueprint $table) {
            $table->id();
            $table->string('question');
            $table->text('response')->nullable();  // A resposta pode ser null até ser processada
            $table->boolean('is_processed')->default(false); // Para indicar quando a resposta estiver pronta
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('chat_responses');
    }
}
