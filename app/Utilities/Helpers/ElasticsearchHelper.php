<?php

namespace App\Utilities\Helpers;

use App\Utilities\Contracts\ElasticsearchHelperInterface;
use Elasticsearch\ClientBuilder;
use Elasticsearch\Common\Exceptions\ElasticsearchException;

class ElasticsearchHelper implements ElasticsearchHelperInterface
{
    protected $client;

    public function __construct()
    {
        $host = [
            [
                'host' => env('ELASTICSEARCH_HOST', 'localhost'),
                'port' => env('ELASTICSEARCH_PORT', '9200'),
            ]
        ];
        $this->client = ClientBuilder::create()->setHosts($host)->build();
    }

    public function storeEmail(string $messageBody, string $messageSubject, string $toEmailAddress): string
    {
        $data = [
            'index' => env('ELASTICSEARCH_INDEX', 'emails'),
            'body' => [
                'message_body' => $messageBody,
                'message_subject' => $messageSubject,
                'to_email_address' => $toEmailAddress,
                'timestamp' => now(),
            ],
        ];
        $result = $this->client->index($data);
        return $result['_id'];
    }

    public function getEmails()
    {
        $params = [
            'index' => env('ELASTICSEARCH_INDEX', 'emails'),
            'body' => [
                'query' => [
                    'match_all' => new \stdClass(),
                ],
            ],
        ];
        try {

            $response = $this->client->search($params);

            $emails = [];
            foreach ($response['hits']['hits'] as $hit) {
                $emails[] = [
                    'email' => $hit['_source']['to_email_address'],
                    'subject' => $hit['_source']['message_subject'],
                    'body' => $hit['_source']['message_body'],
                    'timestamp' => $hit['_source']['timestamp'],
                ];
            }

            return $emails;
        } catch (ElasticsearchException $e) {
            return response()->json(['error' => 'An error occurred while processing your request. Please try again later.'], 500);
        }
    }
}
