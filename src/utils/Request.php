<?php

class Request {
    public function getBody(): array {
        if(!isset($_SERVER["CONTENT_TYPE"])) return [];

        if($_SERVER["CONTENT_TYPE"] === "application/json") {
            return json_decode(file_get_contents("php://input"), TRUE);
        }
    }

    public function getHeaders(): array {
        return getallheaders();
    }

    public function getQueryString(): array {
        parse_str($_SERVER["QUERY_STRING"], $get_array);
        return $get_array;
    }
}