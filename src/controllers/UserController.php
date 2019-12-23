<?php

require_once(__DIR__."/../utils/Response.php");
require_once(__DIR__."/../utils/Request.php");

class UserController {

    public function __construct()
    {
        // $this->service = new UserService();
    }

    public function index(Request $req, Response $res) {
        $res->json()
            ->setMessage("ok")
            ->setData(["user" => "asd"])
            ->send();
    }
}