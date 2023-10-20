<?php

namespace App\Http\Controllers;

use App\Utilities\Contracts\ElasticsearchHelperInterface;
use App\Utilities\Contracts\RedisHelperInterface;
use Illuminate\Http\Request;
use App\Jobs\SendEmailJob;

class EmailController extends Controller
{
    // TODO: finish implementing send method
    public function send(Request $request, $user)
    {

        // Validate the API token
        if ($request->query('api_token') !== 'your_api_token') {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Validate request data
        $request->validate([
            'emails.*.email' => 'required|email',
            'emails.*.subject' => 'required|string',
            'emails.*.body' => 'required|string',
        ]);

        // Parse request data
        $emailsList = $request->input('emails');

        foreach ($emailsList as $emailList) {
            // Dispatch SendEmailJob asynchronously
            SendEmailJob::dispatch($emailList)->onQueue('emails');

            /** @var ElasticsearchHelperInterface $elasticsearchHelper */
            $elasticsearchHelper = app()->make(ElasticsearchHelperInterface::class);
            // TODO: Create implementation for storeEmail and uncomment the following line
            $id = $elasticsearchHelper->storeEmail($emailList['body'], $emailList['subject'], $emailList['email']);

            /** @var RedisHelperInterface $redisHelper */
            $redisHelper = app()->make(RedisHelperInterface::class);
            // TODO: Create implementation for storeRecentMessage and uncomment the following line
            $redisHelper->storeRecentMessage($id, $emailList['subject'], $emailList['email']);

        }

        return response()->json(['message' => 'Emails sent successfully']);
    }

    //  TODO - BONUS: implement list method
    public function list()
    {
        $elasticsearchHelper = app()->make(ElasticsearchHelperInterface::class);
        $emails = $elasticsearchHelper->getEmails();

        //$redisHelper = app()->make(RedisHelperInterface::class);
        //$emails = $redisHelper->getCachedEmails();

        return response()->json($emails);
    }
}
