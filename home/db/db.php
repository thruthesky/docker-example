<?php



class MyDB
{
    public mysqli $connection;

    public function __construct(string $host, string $dbname, string $username, string $passwd, int $port = 0)
    {
        try {
            if (!$port) $port = ini_get("mysqli.default_port");
            $this->connection = new mysqli($host, $username, $passwd, $dbname, $port);
        } catch (Exception $e) {
            $this->handleError($e->getCode() . ': ' . $e->getMessage());
        }
    }

    private function handleError(string $msg, string $sql = '')
    {
        $error_message = ' ------ Mysql error: ' . $msg ."\n" . $sql;
        debug_log($error_message);
        throw new RuntimeException($error_message);
    }

    /**
     * @deprecated Do not use this method. it is not safe. Use it only for test or debug.
     *
     * Performs a query on the database
     *
     * @warning Security warning: SQL injection @see https://www.php.net/manual/en/mysqli.query.php
     * @warning Due to the security reason, this method must be used only internally. It is important to avoid querying
     *  with user data from HTTP input.
     * @warning Avoid to use this method with "SELECT", "UPDATE", "DELETE".
     *
     * @return mixed
     *  Returns false on failure. For successful queries which produce a result set, such as SELECT, SHOW, DESCRIBE or
     *  EXPLAIN, mysqli_query() will return a mysqli_result object. For other successful queries, mysqli_query() will
     *  return true.
     */
    public function query($sql): mixed {
        return $this->connection->query($sql);
    }


    public function parseRecord(array $record, string $type = 'insert') {
        // 입력 받은 배열에서 필드와 값을 분리시켜 각각 $fields 와 $values 로 저장.

        $fields = [];
        $values = [];

        foreach ($record as $k => $v) {
            $fields[] = $k;
            $values[] = $v;
        }

        if ( $type == 'insert' ) {
            $return_fields = implode(',', $fields);
            $return_values = implode(",", array_fill(0, count($values), '?'));
        } else if ( $type == 'update' || $type == 'where' ) {
            $expressions = [];
            foreach( $fields as $field ) {
                $expressions[] = "$field=?";
            }
            $return_fields = implode(" AND ", $expressions);
            $return_values = $values;
        }
        return [ 'fields' => $return_fields, 'values' => $return_values ];
    }
    /**
     *
     * @param string $table - 레코드를 생성 할 테이블
     * @param array $record - 레코드의 필드(키)와 값을 연관 배열로 입력 받는다.
     */
    public function insert(string $table, array $record)
    {

        // Statement 준비
        $stmt = $this->connection->stmt_init();

        // 입력 받은 테이블과 레코드 정보를 바탕으로 SQL 문장을 만든다.
        $parsed = $this->parseRecord( $record );
        $sql = "INSERT INTO $table ( $parsed[fields] ) VALUES ( $parsed[values] )";
        $re = $stmt->prepare($sql);
        if (!$re) {
            $this->handleError($this->connection->error, $sql);
        }


        $values = array_values($record);

        // 저장 할 값의 타입(형)을 계산
        $types = $this->types($values);

        // SQL 문장을 바탕으로 값의 타입(형)과 값을 바인드
        $stmt->bind_param($types, ...$values);

        // 쿼리
        $re = $stmt->execute();
        if (!$re) {
            $this->handleError($this->connection->error, $sql);
        }
    }

    public function delete(string $table, array $conds): bool
    {

        try {
            $stmt = $this->connection->stmt_init();
            $sql = "DELETE FROM $table WHERE age = ?";
            $stmt->prepare($sql);
            $types = $this->types($conds);
            $stmt->bind_param($types, $conds['age']);
            return $stmt->execute();
        } catch (mysqli_sql_exception $e) {
            $this->handleError($e->__toString(), $sql);
            return false;
        }
    }

    public function update(string $table, array $record, array $conds): bool
    {
        
        try {
            
            $stmt = $this->connection->stmt_init();
            
            $update = $this->parseRecord( $record, 'update' );
            $where = $this->parseRecord( $conds, 'where' );
            $sql = "UPDATE $table SET $update[fields] WHERE $where[fields]";
            $stmt->prepare($sql);
            $re = $stmt->prepare($sql);
            if (!$re) {
                $this->handleError($this->connection->error, $sql);
            }
            $values = array_merge( array_values($record), array_values($conds));

            $types = $this->types($values);
            $re = $stmt->bind_param($types, ...$values);

            return $stmt->execute();

            
        } catch (mysqli_sql_exception $e) {
            $this->handleError($e->__toString(), "SQL: " . $sql);
        }

    }


    public function rows(string $table, array $conds = [], $select = '*')
    {
        
        
        try {
            
            $stmt = $this->connection->stmt_init();
            if ( $conds ) {
                $parsed = $this->parseRecord( $conds, 'where' );
                $sql = "SELECT $select FROM $table WHERE $parsed[fields]";
                $stmt->prepare($sql);
                $re = $stmt->prepare($sql);
                if (!$re) {
                    $this->handleError($this->connection->error, $sql);
                }
                $values = array_values($conds);
                $types = $this->types($values);
                $re = $stmt->bind_param($types, ...$values);
            } else {
                $sql = "SELECT $select FROM $table";
                $stmt->prepare($sql);
            }

            
            $stmt->execute();

            $result = $stmt->get_result(); // get the mysqli result
            if ( $result === false ) {

                $this->handleError("SQL ERROR on row()", $sql);
                return [];
            }
            /* fetch associative array */
            $rets = [];
            while ($row = $result->fetch_assoc()) {
                $rets[] = $row;
            }
            return $rets;
        } catch (mysqli_sql_exception $e) {
            $this->handleError($e->__toString(), "SQL: " . $sql);
        }
    }

    
    

    public function row(string $table, array $conds = [], $select = '*')
    {
        $rows = $this->rows($table, $conds, $select);
        if ( ! $rows ) return [];
        return $rows[0];
    }

    public function column(string $table, array $conds = [], $select = '*') {
        $row = $this->row( $table, $conds, $select );
        if ( ! $row ) return null;
    
        return $row[$select];
    }

    public function count(string $table, array $conds = []) {
        return $this->column($table, $conds, "COUNT(*)");
    }


    

    /**
     * @param $val
     * @return string
     */
    private function type(mixed $val): string
    {
        if ($val == '' || is_string($val)) return 's';
        if (is_float($val)) return 'd';
        if (is_int($val)) return 'i';
        return 'b';
    }

    /**
     * @param array $values
     * @return string
     */
    private function types(array $values): string
    {
        $type = '';
        foreach ($values as $val) {
            $type .= $this->type($val);
        }
        return $type;
    }


}

$mysqli = new MyDB('mariadb', 'study', 'study', '12345a,*');

function db(): MyDB
{
    global $mysqli;
    return $mysqli;
}