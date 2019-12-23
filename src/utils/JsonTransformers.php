<?php

require_once(__DIR__ . "/Response.php");

class JsonTransformers {
    public function send(Response $response) {
        header("Content-Type: application/json; charset=UTF-8");
        http_response_code($response->statusCode);

        echo json_encode([
            "data" => $response->data,
            "message" => $response->message,
            "error" => $response->error
        ]);
        die();
    }
}