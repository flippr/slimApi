<?php

class PPush
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

    public function getAllPatient()
    {
        try
        {
            $stmt = $this->conn->query("select * from patient_push");
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

    public function getPatient($id)
    {
        try
        {
            $stmt = $this->conn->prepare("select * from patient_push where id = :id");
            $stmt->bindParam("id", $id);
            $stmt->execute();
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

    public function postPatient($id)
    {
        if ($this->rest->get_request_method() != "POST")
        {
            $status = array('result' => "failed", "message" => "invalid request type");
            $this->rest->response($this->cleaner->json($status), 405);
        }
        $dsid = $id;
        $pushSourceId = $this->source->getSource();
        isset($_REQUEST['patientRid']) ? $patientRid = $_REQUEST['patientRid'] : $patientRid = null;
        isset($_REQUEST['clientRid']) ? $clientRid = $_REQUEST['clientRid'] : $clientRid = null;
        isset($_REQUEST['clientPushId']) ? $clientPushId = $_REQUEST['clientPushId'] : $clientPushId = null;
        isset($_REQUEST['breedRid']) ? $breedRid = $_REQUEST['breedRid'] : $breedRid = null;
        isset($_REQUEST['speciesRid']) ? $speciesRid = $_REQUEST['speciesRid'] : $speciesRid = null;
        isset($_REQUEST['colorRid']) ? $colorRid = $_REQUEST['colorRid'] : $colorRid = null;
        isset($_REQUEST['name']) ? $name = $_REQUEST['name'] : $name = null;
        isset($_REQUEST['birthDate']) ? $birthDate = $_REQUEST['birthDate'] : $birthDate = null;
        isset($_REQUEST['gender']) ? $gender = $_REQUEST['gender'] : $gender = null;
        isset($_REQUEST['weight']) ? $weight = $_REQUEST['weight'] : $weight = null;
        isset($_REQUEST['fixed']) ? $fixed = $_REQUEST['fixed'] : $fixed = null;
        isset($_REQUEST['created']) ? $created = $_REQUEST['created'] : $created = null;
        if ($clientRid != null && $clientPushId != null)
        {
            $request = array(
                "method" => $_SERVER['REQUEST_METHOD'],
                "format" => "json");
            $result = array('error' => "invalid Parameters - (clientRid, clientPushId) one of these values is required to be null");
            $output = array(
                "request" => $request,
                "result" => $result
            );
            $this->rest->response($this->cleaner->json($output), 405);
        }
        try
        {
            if ($stmt = $this->conn->prepare("INSERT INTO patient_push (id, dsid, patientRid, clientRid, clientPushId, breedRid, speciesRid, colorRid,
                name, birthDate, gender, weight, fixed, created, pushSourceId, pushStatusId,
                pushStatusMessage, pushCompletedOn)
                VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)")
            )
            {
                $stmt->execute(array(
                    null,
                    $dsid,
                    $patientRid,
                    $clientRid,
                    $clientPushId,
                    $breedRid,
                    $speciesRid,
                    $colorRid,
                    $name,
                    $birthDate,
                    $gender,
                    $weight,
                    $fixed,
                    $created,
                    $pushSourceId,
                    1,
                    null,
                    null
                ));

                if ($stmt->rowCount() > 0)
                {
                    $output = array(
                        "result" => "success",
                        "message" => "successfully added one record"
                    );
                    $this->rest->response($this->cleaner->json($output), 200);
                }
                else
                {
                    $this->rest->response('', 204); // If no records "No Content" status
                }
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

    public function deletePatient($id)
    {
        try
        {
            $stmt = $this->conn->prepare("delete from patient_push where id=:id");
            $stmt->bindParam("id", $id);
            $stmt->execute();
            if ($stmt->rowCount() > 0)
            {
                $output = array(
                    "result" => "success",
                    "message" => "successfully deleted one record"
                );
                $this->rest->response($this->cleaner->json($output), 200);
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

    //TODO get putPatient setup as a valid route
    public function putPatient($id)
    {
        $request = $this->app->request();
        $name = $request->put('name');
        $url = $request->put('url');

        try
        {
            $sql = "update commodores set url=:url, name=:name where id=:id";
            $s = $this->dbh->prepare($sql);
            $s->bindParam("id", $id);
            $s->bindParam("name", $name);
            $s->bindParam("url", $url);
            $s->execute();
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