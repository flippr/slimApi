<?php

class Key
{
    protected $conn_var;

    protected $conn;

    public function __construct()
    {
        $this->conn = new \Db();
    }

    public function getKey()
    {
        try
        {
            $stmt = $this->conn->query("SELECT ps.description as name, ak.publicKey as publickey
                    FROM api_key ak
	                left join push_source ps
		            on ak.pushSourceId = ps.id");
            $keys = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            if ($stmt->rowCount() > 0)
            {
                foreach ($keys as $key)
                {
                    $user[strtolower($key['name'])] = $key['publickey'];
                }
                return $user;
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