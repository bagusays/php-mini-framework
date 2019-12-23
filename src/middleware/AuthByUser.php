<?php

require_once(__DIR__ . "/../utils/Request.php");
require_once(__DIR__ . "/../utils/Response.php");

class AuthByUser {

    function handler(Request $request, Response $response) {
        if(!isset($request->getBody()["key"])) {
            $response->json()
                ->setMessage("ok")
                ->setError("user Not Authorized")
                ->setStatusCode(401)
                ->send();
        }
    }
}