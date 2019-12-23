<?php

return [
    [
        "endpoint" => "/", 
        "controller" => "IndexController@index", 
        "method" => "POST",
        // "middleware" => ["authByRole", "authByUser"]
    ],
    [
        "endpoint" => "/index2", 
        "controller" => "IndexController@index2", 
        "method" => "GET",
        // "middleware" => ["authByRole", "authByUser"]
    ],
    [
        "endpoint" => "/user", 
        "controller" => "UserController@index", 
        "method" => "GET",
        "middleware" => ["authByRole"]
    ],

    ["endpoint" => "*"] // 404 not found route
];