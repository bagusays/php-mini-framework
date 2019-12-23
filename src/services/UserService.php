<?php

require_once(__DIR__ . "/../repository/UserRepository.php");

class UserService {

    private $userRepo;

    public function __construct()
    {
        $this->userRepo = new UserRepository();
    }

    public function getUser() {
        return $this->userRepo->getUser();
    }
}