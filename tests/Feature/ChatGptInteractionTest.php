<?php

namespace Tests\Feature;

use DB;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ChatGptInteractionTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test to verify ChatGPT response with a simple question.
     *
     * @return void
     */
    public function test_chatgpt_simple_question_response()
    {
        // Simulate ChatGPT response using the Http::fake() function
        Http::fake([
            'https://api.openai.com/v1/chat/completions' => Http::response([
                'choices' => [
                    ['message' => ['content' => 'Based on the information provided, I could not identify a specific relationship between Abu Nayem and Aftab Girach.']]
                ]
            ], 200)
        ]);

        // Send a POST request to the route that queries ChatGPT
        $response = $this->post('/ask', [
            'question' => 'What is the relationship between Abu Nayem and Aftab Girach?'
        ]);

        // Verify if the response contains the expected message
        $response->assertStatus(200)
            ->assertSee('I could not identify a specific relationship between Abu Nayem and Aftab Girach');
    }

    /**
     * Test to verify ChatGPT response with emails as context.
     *
     * @return void
     */
    public function test_chatgpt_with_email_context_response()
    {
        // Simulate ChatGPT response using the Http::fake() function
        Http::fake([
            'https://api.openai.com/v1/chat/completions' => Http::response([
                'choices' => [
                    ['message' => ['content' => 'Based on the emails exchanged, it seems that Abu Nayem and Aftab Girach have a professional relationship, likely collaborating on a project.']]
                ]
            ], 200)
        ]);

        // Simulate some example emails in the database
        $this->seedEmails();

        // Send a POST request to the route that queries ChatGPT with emails as context
        $response = $this->post('/ask', [
            'question' => 'Based on the emails between Abu Nayem and Aftab Girach, what is their relationship?'
        ]);

        // Verify if the response contains the expected message
        $response->assertStatus(200)
            ->assertSee('it seems that Abu Nayem and Aftab Girach have a professional relationship');
    }

    /**
     * Test to verify a generic question without email context.
     *
     * @return void
     */
    public function test_chatgpt_generic_question_response()
    {
        // Simulate ChatGPT response using the Http::fake() function
        Http::fake([
            'https://api.openai.com/v1/chat/completions' => Http::response([
                'choices' => [
                    ['message' => ['content' => 'I\'m sorry, but I don\'t have enough context or details about Aftab Girach.']]
                ]
            ], 200)
        ]);

        // Send a POST request to the route that queries ChatGPT
        $response = $this->post('/ask', [
            'question' => 'Who is Aftab Girach?'
        ]);

        // Verify if the response contains the expected message
        $response->assertStatus(200)
            ->assertSee('I don\'t have enough context or details about Aftab Girach');
    }

    /**
     * Simula emails de exemplo no banco de dados.
     */
    private function seedEmails()
    {
        DB::table('emails')->insert([
            [
                'from_email' => 'naeem043@gmail.com',
                'to_email' => 'aftabgirach@gmail.com',
                'subject' => 'Project collaboration',
                'body_text' => 'Let\'s discuss the upcoming project collaboration next week.',
            ],
            [
                'from_email' => 'aftabgirach@gmail.com',
                'to_email' => 'naeem043@gmail.com',
                'subject' => 'Follow-up',
                'body_text' => 'Sure, let\'s meet on Tuesday to go over the project details.',
            ]
        ]);
    }
}
