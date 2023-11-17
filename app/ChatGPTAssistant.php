<?php

declare(strict_types=1);
namespace app;

use Exception;

class ChatGPTAssistant
{
    private $api_key;
    private $assistant_id;

    public function __construct(string $api_key, string $assistant_id)
    {
        $this->api_key = $api_key;
        $this->assistant_id = $assistant_id;
    }

    private function callOpenAI($endpoint, $method = 'POST', $data = [], $contentType = '"Content-Type: application/json",', $postData = false)
    {
        $curl = curl_init();
        $url = "https://api.openai.com/v1" . $endpoint;

        $httpHeader = [
            "Authorization: Bearer $this->api_key",
            "OpenAI-Beta: assistants=v1"
        ];

        if ($method == 'POST') {
            $httpHeader[] = "Content-Type: application/json";
        }

        $options = [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HTTPHEADER => $httpHeader,
        ];

        if($postData){
            $options[CURLOPT_POSTFIELDS] = json_encode($data);
        }

        curl_setopt_array($curl, $options);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            throw new Exception("cURL Error: $err");
        } else {
            return json_decode($response, true);
        }
    }

    public function createThread()
    {
        return $this->callOpenAI('/threads');
    }

    public function addMessageToThread($threadId, $messageContent)
    {
        $data = [
            "role" => "user",
            "content" => $messageContent
        ];
        return $this->callOpenAI("/threads/$threadId/messages", 'POST', $data, '"Content-Type: application/json",', true);
    }

    public function runAssistant($threadId)
    {
        $data = [
            "assistant_id" => $this->assistant_id
        ];
        return $this->callOpenAI("/threads/$threadId/runs", 'POST', $data, '"Content-Type: application/json",', true);
    }

    public function retrieveRun($threadId, $runId)
    {
        return $this->callOpenAI("/threads/$threadId/runs/$runId", 'GET', [], '');
    }

    public function listMessages($threadId)
    {
        return $this->callOpenAI("/threads/$threadId/messages", 'GET');
    }
}
