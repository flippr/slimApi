<?php

class CatchAll
{

    protected $conn;

    protected $rest;

    protected $app;

    public function __construct()
    {
        $this->rest = new \Rest();
        $this->source = new \Source();
        $this->cleaner = new \Cleaner();
    }

    public function catchAll()
    {
        $request = array(
            "method" => $_SERVER['REQUEST_METHOD'],
            "format" => "json");
        $result = array('error' => "Invalid Parameters");
        $output = array(
            "request" => $request,
            "result" => $result
        );
        $this->rest->response($this->cleaner->json($output), 405);
    }
} 