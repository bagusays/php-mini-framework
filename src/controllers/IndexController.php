<?php

require_once(__DIR__."/../utils/Response.php");
require_once(__DIR__."/../utils/Request.php");

class IndexController {
    
    public function __construct()
    {

    }

    public function index(Request $req, Response $res) {
        $res->json()
            ->setMessage("ok")
            ->setData($req->getBody())
            ->send();
    }

    public function index2(Request $req, Response $res) {
        $res->json()
            ->setMessage("ok")
            ->setData(["user" =>"index2"])
            ->send();
    }
}