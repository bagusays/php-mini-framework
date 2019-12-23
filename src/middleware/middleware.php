<?php

require_once("AuthByRole.php");
require_once("AuthByUser.php");

/**
*   @key          Name of middleware
*   @value      Instance of class
*/
return [
    "authByRole" => new AuthByRole(),
    "authByUser" => new AuthByUser()
];