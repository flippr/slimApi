<?php

class Source
{
    protected $conn_var;

    protected $conn;

    public function __construct()
    {
        $this->conn = new \Db();
    }

    public function getSource()
    {
        try
        {
            $stmt = $this->conn->prepare("SELECT id FROM push_source where description = :description");
            $stmt->execute(array(':description' => $GLOBALS['user']));
            $user = $stmt->fetch();
            if ($stmt->rowCount() > 0)
            {
                return $user['id'];
            }
            else
            {
                return False;
            }
        }
        catch (\PDOException $e)
        {

        }
    }
} 