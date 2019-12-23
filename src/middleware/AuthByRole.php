<?php

require_once(__DIR__ . "/../utils/Request.php");
require_once(__DIR__ . "/../utils/Response.php");

class AuthByRole {

    function handler(Request $request, Response $response) {
        if(!array_key_exists("Authorization", $request->getHeaders())) {
            $response->json()
                ->setMessage("ok")
                ->setError("Not Authorized")
                ->setStatusCode(401)
                ->send();
        }
    }
}