<?php

class CPush
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

    public function getClients()
    {
        try
        {
            $stmt = $this->conn->query("select * from client_push");
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

    public function getClientById($id)
    {
        try
        {
            $stmt = $this->conn->prepare("select * from client_push where id = :id");
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
                $status = array("result" => "error", "message" => 'could not find a client with that id');
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

    public function postClientById($id)
    {
        if ($this->rest->get_request_method() != "POST")
        {
            $status = array('result' => "failed", "message" => "invalid request type");
            $this->rest->response($this->cleaner->json($status), 405);
        }
        $dsid = $id;
        $pushSourceId = $this->source->getSource();
        isset($_REQUEST['firstName']) ? $firstName = $_REQUEST['firstName'] : $firstName = null;
        isset($_REQUEST['lastName']) ? $lastName = $_REQUEST['lastName'] : $lastName = null;
        isset($_REQUEST['email']) ? $email = $_REQUEST['email'] : $email = null;
        isset($_REQUEST['homePhone']) ? $homePhone = $_REQUEST['homePhone'] : $homePhone = null;
        isset($_REQUEST['mobilePhone']) ? $mobilePhone = $_REQUEST['mobilePhone'] : $mobilePhone = null;
        isset($_REQUEST['workPhone']) ? $workPhone = $_REQUEST['workPhone'] : $workPhone = null;
        isset($_REQUEST['addressLineOne']) ? $addressLineOne = $_REQUEST['addressLineOne'] : $addressLineOne = null;
        isset($_REQUEST['addressLineTwo']) ? $addressLineTwo = $_REQUEST['addressLineTwo'] : $addressLineTwo = null;
        isset($_REQUEST['city']) ? $city = $_REQUEST['city'] : $city = null;
        isset($_REQUEST['state']) ? $state = $_REQUEST['state'] : $state = null;
        isset($_REQUEST['country']) ? $country = $_REQUEST['country'] : $country = null;
        isset($_REQUEST['zip']) ? $zip = $_REQUEST['zip'] : $zip = null;
        isset($_REQUEST['preferredContactTypeId']) ? $preferredContactTypeId = $_REQUEST['preferredContactTypeId'] : $preferredContactTypeId = null;
        try
        {
            if ($stmt = $this->conn->prepare("INSERT INTO client_push (id, dsid, clientRid, firstName, lastName, email, homePhone, mobilePhone, workPhone,
                createdDateTime, defaultClinicRid, addressLineOne, addressLineTwo, city, state, country, zip,
                preferredContactTypeId, pushSourceId, pushStatusId, pushStatusMessage, pushCompletedOn)
                VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)")
            )
            {
                $stmt->execute(array(
                    null,
                    $dsid,
                    null,
                    $firstName,
                    $lastName,
                    $email,
                    $homePhone,
                    $mobilePhone,
                    $workPhone,
                    null,
                    null,
                    $addressLineOne,
                    $addressLineTwo,
                    $city,
                    $state,
                    $country,
                    $zip,
                    $preferredContactTypeId,
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

    /**
     * @param $id
     */
    public function deleteClientById($id)
    {
        try
        {

            $stmt = $this->conn->prepare("delete from client_push where id=:id");
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

    //TODO get put client setup as a valid route
    public function putClientById($id)
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