<?php
if ( ! defined( "ABSPATH" ) ) exit;

class AAA_AI_Connector {
    public static function ask( $user_message, $context ) {
        $api_key = get_option( "aaa_api_key" );
        if ( empty( $api_key ) ) return "Please add your OpenRouter API key in Astra AI Settings.";

        $system_prompt = "You are an expert WordPress and Astra Theme assistant. Give short, clear, step-by-step answers. Use the provided settings for specific advice. Only answer WordPress or Astra related questions.";
        $full_message  = $context . "\n\nUser Question: " . $user_message;

        $response = wp_remote_post( "https://openrouter.ai/api/v1/chat/completions", [
            "timeout" => 30,
            "headers" => [
                "Authorization" => "Bearer " . $api_key,
                "Content-Type"  => "application/json",
                "HTTP-Referer"  => get_site_url(),
                "X-Title"       => "Astra AI Assistant",
            ],
            "body" => json_encode([
                "model"    => "anthropic/claude-haiku-4-5",
                "messages" => [
                    [ "role" => "system",  "content" => $system_prompt ],
                    [ "role" => "user",    "content" => $full_message  ],
                ],
                "max_tokens" => 500,
            ]),
        ]);

        if ( is_wp_error( $response ) ) return "Connection error: " . $response->get_error_message();
        $body = json_decode( wp_remote_retrieve_body( $response ), true );
        return $body["choices"][0]["message"]["content"] ?? "No response from AI.";
    }
}
