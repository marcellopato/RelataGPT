<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChatFormTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Testa se o campo 'question' é obrigatório.
     *
     * @return void
     */
    public function test_the_question_field_is_required()
    {
        $response = $this->post('/ask', []); // Envia uma requisição sem o campo 'question'

        $response->assertSessionHasErrors(['question']); // Verifica se há erro de validação
    }

    /**
     * Testa se o campo 'question' deve ser uma string.
     *
     * @return void
     */
    public function test_the_question_field_must_be_a_string()
    {
        $response = $this->post('/ask', ['question' => 12345]); // Envia um número

        $response->assertSessionHasErrors(['question']); // Verifica se há erro de validação
    }

    /**
     * Testa se o campo 'question' aceita uma string válida.
     *
     * @return void
     */
    public function test_the_question_field_accepts_valid_string_input()
    {
        $response = $this->post('/ask', ['question' => 'What is the relationship between Abu Nayem and Aftab Girach?']); // Envia uma string válida

        $response->assertSessionDoesntHaveErrors(['question']); // Verifica se não há erro de validação
    }
}
