<?php
/**
 * Created by PhpStorm.
 * User: Physics_VR
 * Date: 12/03/2018
 * Time: 12:13
 */

class DBQuerier
{

    private $conn;
    private $stmt;
    private $res;


    public function __construct($conn) {
        $this->conn = $conn;

    }

    public function setStatement($stmt) {
       $this->stmt = $this->conn->stmt_init();

    }

    public function getRes() {
        $stmt = $conn_sql->stmt_init();
        return $this->res;
    }

}