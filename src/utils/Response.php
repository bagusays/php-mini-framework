<?php

require_once(__DIR__."/JsonTransformers.php");

class Response {

    public $type;
    public $message;
    public $data;
    public $error;
    public $statusCode = 200;

    public function json(): Response {
        $this->type = new JsonTransformers();
        return $this;
    }

    public function setMessage(string $message): Response {
        $this->message = $message;
        return $this;
    }

    public function setData($data): Response {
        $this->data = $data;
        return $this;
    }

    public function setError($error): Response {
        $this->error = $error;
        return $this;
    }

    public function setStatusCode(int $statusCode): Response {
        $this->statusCode = $statusCode;
        return $this;
    }

    public function send() {
        $this->type->send($this);
    }
}