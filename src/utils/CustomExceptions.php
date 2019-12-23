<?php

require_once(__DIR__ . "/Response.php");

class EndpointNotFoundException extends \Exception {
    public function __construct()
    {
        $res = new Response();
        $res->json()
            ->setError("404 Aaaaaghhh! The cat is gone")
            ->setStatusCode(404)
            ->send();
    }
}

class MethodNotAllowedException extends \Exception {
    public function __construct()
    {
        $res = new Response();
        $res->json()
            ->setError("Method not allowed")
            ->setStatusCode(405)
            ->send();
    }
}
