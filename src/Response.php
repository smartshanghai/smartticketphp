<?php

namespace smartshanghai\smartticketphp;

class Response {
    private $success;
    private $message;
    private $data;
    private $executionTime;

    public function __construct($wasSuccessful, $message, $data, $executionTime) {
        $this->success = $wasSuccessful;
        $this->message = $message;
        $this->data = $data;
        $this->executionTime =  $executionTime;
    }

    public function wasSuccessful() {
        return $this->success;
    }

    public function getMessage() {
        return $this->message;
    }

    public function getData() {
        return $this->data;
    }

    public function getExecutionTime() {
        return $this->executionTime;
    }
}