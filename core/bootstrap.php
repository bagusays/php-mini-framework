<?php

require_once(__DIR__.'/../src/utils/CustomExceptions.php');
require_once(__DIR__.'/../src/utils/Request.php');
require_once(__DIR__.'/../src/utils/Response.php');
require_once(__DIR__.'/DB.php');

/*
* ------------------------------------------------------
*	Load env config
* ------------------------------------------------------
*/
$applicationEnv = Dotenv\Dotenv::create(__DIR__, '../config/app.env');
$dbEnv = Dotenv\Dotenv::create(__DIR__, '../config/db.env');
$applicationEnv->load();
$dbEnv->load();

$router = require_once(__DIR__ .' /../src/router.php');

foreach($router as $r) {
    if($r["endpoint"] === "*") throw new EndpointNotFoundException(); // asterisk mean 404 not found

    if(parse_url($_SERVER["REQUEST_URI"])["path"] !== $r["endpoint"]) {
        continue;
    }


    /*
    * ------------------------------------------------------
    *	Request method handler
    * ------------------------------------------------------
    */
    if($r["method"] !== null) { // check if method null or not
        if($_SERVER["REQUEST_METHOD"] !== $r["method"]) { // check if method is not equal request method
            throw new MethodNotAllowedException();
        }
    } else {
        throw new Error("method must defined.");
    }


    /*
    * ------------------------------------------------------
    *	Prepare for dependency injection
    * ------------------------------------------------------
    */
    $request = new Request();
    $response = new Response();


    /*
    * ------------------------------------------------------
    *	Middleware handler
    * ------------------------------------------------------
    */
    if(isset($r["middleware"])) {
        $middlewareList = require_once(__DIR__ ." /../src/middleware/middleware.php"); // importing list of middleware by user
        foreach($r["middleware"] as $m) {
            if(!array_key_exists($m, $middlewareList)) { // check if list of middleware contains routerMiddleware
                throw new Exception($m . " is not found.");
            }
            
            $class_methods = get_class_methods($middlewareList[$m]); // get all class methods
            if(!in_array("handler", $class_methods)) { // check if handler present in class method or not
                throw new Exception("You must define handler function inside " . $middlewareList[$m] . " class");
            }
            
            $middlewareList[$m]->handler($request, $response); // call handler function
        }
    }


     /*
    * ------------------------------------------------------
    *	Call the controller
    * ------------------------------------------------------
    */
    $controllerPath = "/src/controllers/";
    $controller = explode("@", $r["controller"]); // spliting class name and method by @
    $class = dirname(__DIR__) . $controllerPath . $controller[0] . ".php";
    if (file_exists($class)) { // check file is exist or not
        require_once($class); // require based on file/class name
    } else {
        throw new Exception($controller[0] . " class is not found");
    }

    $instance = new $controller[0](); // make instance of class
    $callMethod = call_user_func(array($instance, $controller[1]), $request, $response); // call the function
    if(!$callMethod) {
        throw new Exception("Method " . $controller[1] . " is not found");
    }

    break;
}