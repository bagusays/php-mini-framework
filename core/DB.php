<?php

// require_once (__DIR__ . "/../db_config.php");
// require_once (__DIR__ .  "/../utils/HttpStatusCode.php");
// require_once (__DIR__ .  "/../utils/ReturnContent.php");

class DBConnection {
    private static $connection;
    
    public static function getConnection() {
        try {
            if(getenv("dbhost") === false | getenv("dbname") === false || getenv("dbuser") === false || getenv("dbpass") === false) {
                throw new Exception("You must configure config/db.env file before using the database");
            }
            if(self::$connection == null) {
                $conn = new PDO(sprintf("mysql:host=%s;dbname=%s", getenv("dbhost"), getenv("dbname")), getenv("dbuser"), getenv("dbpass"), [PDO::MYSQL_ATTR_MULTI_STATEMENTS => false]);
                $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                self::$connection = $conn;
                return $conn;
            }
            return self::$connection;
        } catch (PDOException $e) {
            throw new Exception($e);
        }
    }
}

class DB {
    private static $connection = null;
    private static $stmt;
    private static $isTransaction = false;
    private static $query;
    private static $params;
    private static $paginationData = [
        "isPaginate" => false,
        "totalData" => 0,
        "lastPage" => 0
    ];

    public static function commit() {
        if (self::$connection->inTransaction()) {
            self::$connection->commit();
            self::$connection = null;
            self::$isTransaction = false;
        }
    }

    public static function rollback() {
        if (self::$connection->inTransaction()) {
            self::$connection->rollback();
            self::$connection = null;
            self::$isTransaction = false;
        }
    }

    public static function beginTransaction() {
        if(self::$connection == null)  {
            self::$connection = DBConnection::getConnection();
        }
        self::$isTransaction = true;
        self::$connection->beginTransaction();
    }

    public static function query(string $query){
        if(self::$connection === null)  {
            self::$connection = DBConnection::getConnection();
        }
        
        self::$query = $query;

        return new static;
    }

    public static function bindParams(array $params) {
        try {            
            self::$params = $params;

            self::$stmt = self::$connection->prepare(self::$query);
            foreach($params as $key => $value) {
                self::$stmt->bindValue($key , $value);
            }
    
            return new static;
        }catch(PDOException $e) {
            return self::errorHandling($e);
        }
    }

    public static function get(){
        try{
            if(self::$params == null) {
                self::$stmt = self::$connection->prepare(self::$query);
            }

            self::$stmt->execute();
            self::$stmt->setFetchMode(PDO::FETCH_ASSOC);

            $result = [];
            while($row = self::$stmt->fetch()) {
                array_push($result, $row);
            }

            if(self::$paginationData["isPaginate"]) {
                $result = [
                    "data" => $result,
                    "totalData" => self::$paginationData["totalData"],
                    "lastPage" => self::$paginationData["lastPage"],
                    "currentPage" => self::$paginationData["currentPage"],
                    "dataPerPage" => self::$paginationData["dataPerPage"]
                ];
            }

            return $result;
        } catch(PDOException $e) {
            if (self::$connection->inTransaction()) {
                self::$connection->rollBack();
            }
            return self::errorHandling($e);
        } finally {
            if (!self::$isTransaction) {
                self::$connection = null;
            }
            self::$stmt = null;
            self::$query = null;
            self::$params = null;
        }
    }
    
    public static function execute() {
        try {
            if(self::$params == null) {
                self::$stmt = self::$connection->prepare(self::$query);
            }
            return self::$stmt->execute();
        } catch(PDOException $e) {
            if (self::$connection->inTransaction()) {
                self::$connection->rollBack();
            }
            return self::errorHandling($e);
        } finally {
            if (!self::$isTransaction) {
                self::$connection = null;
            }
            self::$stmt = null;
            self::$query = null;
            self::$params = null;
        }
    }

    public static function getLastInsertedId() {
        return self::$connection->lastInsertId();
    }

    public static function paginate(int $itemsPerPage, int $selectedPage) {
        if(self::$params == null) {
            self::$stmt = self::$connection->prepare(self::$query);
        }

        self::$stmt->execute();
        self::$stmt->setFetchMode(PDO::FETCH_ASSOC);
        $totalData = self::$stmt->rowCount();

        self::$paginationData["isPaginate"] = true;
        self::$paginationData["lastPage"] = (int)ceil($totalData / $itemsPerPage);
        self::$paginationData["totalData"] = $totalData;
        self::$paginationData["currentPage"] = $selectedPage;
        self::$paginationData["dataPerPage"] = $itemsPerPage;

        $offset = ($selectedPage - 1) * $itemsPerPage;
        self::$query .= " LIMIT " . $offset . ", " . $itemsPerPage;
        self::$stmt = self::$connection->prepare(self::$query);

        if(self::$params !== null) {
            foreach(self::$params as $key => $value) {
                self::$stmt->bindValue($key , $value);
            }
        }

        return new static;
    }

    public function errorHandling($e) {
        self::$stmt = null;
        self::$query = null;

        $res = [
            "isError" => true,
            "errno" => $e->getCode(),
            "getMessage" => $e->getMessage()
        ];

        ReturnContent::json($e->getMessage(), null, HttpStatusCode::internalServerError);
    }
}