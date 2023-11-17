<?php
use app\ChatGPTAssistant;

require_once 'app/ChatGPTAssistant.php';

$apiKey = 'YourAPIKey'; // You can get it from https://platform.openai.com/
$assistantId = 'YourAssistanId'; // You can create your assistant from https://platform.openai.com/assistants then get the id

$assistant = new ChatGPTAssistant($apiKey, $assistantId); // create assistant
$threadResponse = $assistant->createThread(); // create thread
$threadId = $threadResponse['id']; // get thread id

$messageResponse = $assistant->addMessageToThread($threadId, 'Input question from user.'); // wrote the user's question
$runResponse = $assistant->runAssistant($threadId); // run the assistant

// check is assistant returned id
if (isset($runResponse['id'])) { 
    $runId = $runResponse['id'];

    sleep(5); // wait 5 seconds (its estimated, it should be loop)
    $retrieveRun = $assistant->retrieveRun($threadId, $runId); // retrive run process for check the status
    $runStatus = $retrieveRun['status']; // get status

    // if everything fine
    if ($runStatus === 'completed') {
        $assistantResponse = $assistant->listMessages($threadId); // list the messages from thread
        $assistantAnswer = $assistantResponse['data'][0]['content'][0]['text']['value']; // get the assistant's answer
    } else {
        echo "Couldn't completed";
        var_dump($retrieveRun);
        exit;
    }
} else {
    echo "Run id couldn't found.";
}
