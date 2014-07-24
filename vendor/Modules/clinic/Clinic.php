<?php

class Clinic
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

    public function postClinicById()
    {
        if ($this->rest->get_request_method() != "POST")
        {
            $status = array('result' => "failed", "message" => "invalid request type");
            $this->rest->response($this->cleaner->json($status), 405);
        }

        try
        {

            // dsid,companyid,pimsCode,pushSourceId,pimsSoftwareId

            $pimsSoftwareId = 1;
            $pushSourceId = $this->source->getSource();
            if (isset($_REQUEST['id']))
            {
                $pimsCode = $_REQUEST['id'];
            }
            else
            {
                $status = array("result" => "error", "message" => 'Missing required parameter (id)');
                $this->rest->response($this->cleaner->json($status), 200);
            }
            isset($_REQUEST['name']) ? $name = $_REQUEST['name'] : $name = null;
            isset($_REQUEST['email']) ? $email = $_REQUEST['email'] : $email = null;
            isset($_REQUEST['phone']) ? $phone = $_REQUEST['phone'] : $phone = null;
            isset($_REQUEST['addressLineOne']) ? $addressLineOne = $_REQUEST['addressLineOne'] : $addressLineOne = null;
            isset($_REQUEST['addressLineTwo']) ? $addressLineTwo = $_REQUEST['addressLineTwo'] : $addressLineTwo = null;
            isset($_REQUEST['city']) ? $city = $_REQUEST['city'] : $city = null;
            isset($_REQUEST['state']) ? $state = $_REQUEST['state'] : $state = null;
            isset($_REQUEST['country']) ? $country = $_REQUEST['country'] : $country = null;
            isset($_REQUEST['zip']) ? $zip = $_REQUEST['zip'] : $zip = null;

            $address = array(
                "addressLineOne" => $addressLineOne,
                "addressLineTwo" => $addressLineTwo,
                "city" => $city,
                "state" => $state,
                "country" => $country,
                "zip" => $zip);

            $stmt = $this->conn->prepare("select * from data_source_pims where pimsCode = :id");
            $stmt->execute(array(":id" => $pimsCode));
            $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            if ($stmt->rowCount() > 0)
            {
                foreach ($result as $key => $value)
                {
                    $columns[$key] = $this->cleaner->stripChars($value);
                }
                $status = array($columns);
                $newDsid = $status[0][0]['dsid'];

                $output = array(
                    "message" => "the unique id that was entered is already tied to a data source",
                    "dsid" => $newDsid
                );
                $this->rest->response($this->cleaner->json($output), 200);
            }
            else
            {
                $addressId = $this->createAddress($address);
                $companyId = $this->createCompany($name, $phone, $email, $addressId);
                if ($companyId)
                {
                    $clinicDsid = $this->createDsid($name, $pimsSoftwareId, $companyId);

                    if ($clinicDsid)
                    {
                        $stmt = $this->conn->prepare("INSERT INTO data_source_pims (id, dsid, companyId, pimsCode, pushSourceId, pimsSoftwareId) VALUES (?,?,?,?,?,?)");
                        $stmt->execute(array(
                            null,
                            $clinicDsid,
                            $companyId,
                            $pimsCode,
                            $pushSourceId,
                            $pimsSoftwareId
                        ));
                        if ($stmt->rowCount() > 0)
                        {
                            $output = array(
                                "result" => "success",
                                "dsid" => $clinicDsid
                            );
                            $this->rest->response($this->cleaner->json($output), 200);
                        }
                        else
                        {
                            $request = array(
                                "method" => $_SERVER['REQUEST_METHOD'],
                                "format" => "json");
                            $result = array(
                                "error" => 'Unable to generate new records');
                            $output = array(
                                "request" => $request,
                                "result" => $result
                            );
                            $this->rest->response($this->cleaner->json($output), 405);
                        }
                    }
                    else
                    {
                        $request = array(
                            "method" => $_SERVER['REQUEST_METHOD'],
                            "format" => "json");
                        $result = array(
                            "error" => 'Unable to generate new data source from records');
                        $output = array(
                            "request" => $request,
                            "result" => $result
                        );
                        $this->rest->response($this->cleaner->json($output), 405);
                    }

                }
                else
                {
                    $request = array(
                        "method" => $_SERVER['REQUEST_METHOD'],
                        "format" => "json");
                    $result = array(
                        "error" => 'Unable to generate company information from records');
                    $output = array(
                        "request" => $request,
                        "result" => $result
                    );
                    $this->rest->response($this->cleaner->json($output), 405);
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

    public function createAddress(array $address)
    {
        try
        {
            $stmt = $this->conn->prepare("INSERT INTO address (id, addressLineOne, addressLineTwo, city, state, country, zip)
                VALUES (?,?,?,?,?,?,?)");
            $stmt->execute(array(
                null,
                $address['addressLineOne'],
                $address['addressLineTwo'],
                $address['city'],
                $address['state'],
                $address['country'],
                $address['zip']
            ));
            if ($stmt->rowCount() > 0)
            {
                return $this->conn->lastInsertId();
            }
            else
            {
                return null;
            }
        }
        catch (\PDOException $e)
        {
            return null;
        }

    }

    public function createCompany($name, $phone, $email, $addressId)
    {
        $dt = new DateTime();
        $created = $dt->format('Y-m-d H:i:s');

        $stmt = $this->conn->prepare("INSERT INTO company (id, name, phoneNumber, email, created, representativeId, addressId, mergewordSetId)
                VALUES (?,?,?,?,?,?,?,?)");
        $stmt->execute(array(
            null,
            $name,
            $phone,
            $email,
            $created,
            null,
            $addressId,
            0
        ));
        if ($stmt->rowCount() > 0)
        {
            return $this->conn->lastInsertId();
        }
        else
        {
            return FALSE;
        }
    }

    public function createDsid($name, $pimsSoftwareId, $companyId)
    {
        $stmt = $this->conn->prepare("INSERT INTO data_source (id, companyId, active, purposeId, pimsSoftwareId, softwareVersionId, name, ipMask,createUsersEnabled,syncUserClientEnabled,clientSoftwareVersionId,groupId,lastDisconnect,syncFailureMessageId)
                VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
        $stmt->execute(array(
            null,
            $companyId,
            1,
            0,
            $pimsSoftwareId,
            null,
            $name,
            null,
            0,
            0,
            null,
            null,
            null,
            null
        ));
        if ($stmt->rowCount() > 0)
        {
            return $this->conn->lastInsertId();
        }
        else
        {
            return FALSE;
        }
    }

    public function createClinic()
    {

    }
}