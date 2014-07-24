<?php

class Patient
{
    protected $conn_var;

    protected $conn;

    protected $rest;

    protected $app;

    public function __construct()
    {
        $this->conn = new \Db();
        $this->rest = new \Rest();
        $this->source = new \Source();
        $this->cleaner = new \Cleaner();
    }

    public function getAllPatient($dsid)
    {
        try
        {
            $stmt = $this->conn->prepare("select * from patient where dsid = :dsid");
            $stmt->execute(array(":dsid" => $dsid));
            $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            if ($stmt->rowCount() > 0)
            {
                foreach ($result as $key => $value)
                {
                    $columns[$key] = $this->cleaner->stripChars($value);
                }
                $status = array($columns);
                $this->rest->response($this->cleaner->json($status), 200);

            }
            else
            {
                $this->rest->response('', 204); // If no records "No Content" status
            }

        }
        catch (\PDOException $e)
        {
            $request = array(
                "method" => $_SERVER['REQUEST_METHOD'],
                "format" => "json");
            $result = array(
                "error" => 'Exception: ' . $e->getMessage());
            $output = array(
                "request" => $request,
                "result" => $result
            );

            $this->rest->response($this->cleaner->json($output), 405);
        }
    }

    public function getPatientByRid($dsid, $rid)
    {
        try
        {
            $stmt = $this->conn->prepare("select * from patient where dsid = :dsid AND rid = :rid");
            $stmt->execute(array(":dsid" => $dsid, ":rid" => $rid));
            $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            if ($stmt->rowCount() > 0)
            {
                //print_r($result);
                foreach ($result as $key => $value)
                {
                    $columns[$key] = $this->cleaner->stripChars($value);
                }
                $status = array($columns);
                $this->rest->response($this->json($status), 200);
            }
            else
            {
                $status = array("result" => "error", "message" => 'could not find a patient with that id');
                $this->rest->response($this->cleaner->json($status), 200);
            }
        }
        catch (\PDOException $e)
        {
            $request = array(
                "method" => $_SERVER['REQUEST_METHOD'],
                "format" => "json");
            $result = array(
                "error" => 'Exception: ' . $e->getMessage());
            $output = array(
                "request" => $request,
                "result" => $result
            );

            $this->rest->response($this->cleaner->json($output), 405);
        }
    }
}