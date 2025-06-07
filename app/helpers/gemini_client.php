<?php

class GeminiClient
{
    private $api_key;
    // Đổi sang model mới, ví dụ: gemini-1.5-flash (hoặc gemini-2.0-flash nếu đúng tên model bạn được trả về)
    private $api_url = 'https://generativelanguage.googleapis.com/v1/models/gemini-1.5-flash:generateContent';

    public function __construct($api_key)
    {
        $this->api_key = $api_key;
    }

    public function generateContent($prompt)
    {
        $url = $this->api_url . '?key=' . urlencode($this->api_key);

        $data = [
            "contents" => [
                [
                    "parts" => [
                        ["text" => $prompt]
                    ]
                ]
            ]
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json'
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            throw new Exception('Curl error: ' . curl_error($ch));
        }
        curl_close($ch);

        return json_decode($response, true);
    }

    // Hàm lấy danh sách các model khả dụng
    public function listModels()
    {
        $url = 'https://generativelanguage.googleapis.com/v1/models?key=' . urlencode($this->api_key);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json'
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            throw new Exception('Curl error: ' . curl_error($ch));
        }
        curl_close($ch);
        return json_decode($response, true);
    }
}