<?php
use Tests\TestCase;
use Elasticsearch\Client;
use Illuminate\Support\Facades\Mail;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EmailApiTest extends TestCase
{
    use RefreshDatabase;
    public function testSendEmails()
    {
        // Arrange: Prepare the input data
        $requestData = [
            'emails' => [
                [
                    'email' => 'recipient@example.com',
                    'subject' => 'Test Subject',
                    'body' => 'Test Body',
                ],
            ],
        ];

        // Act: Send a POST request to the API endpoint
        $response = $this->json('POST','/api/{user}/send?api_token=your_api_token', $requestData);

        // Assert: Check the response
        $response->assertStatus(200)
            ->assertJson(['message' => 'Emails sent successfully']);

    }
}
