<?php

class MyDB {
    public mysqli $connection;
    public function __construct(string $host, string $dbname, string $username, string $passwd, int $port = 0) {
        try {
            if ( !$port ) $port = ini_get("mysqli.default_port");
            $this->connection = new mysqli($host, $username, $passwd, $dbname, $port);
        } catch(Exception $e) {
            $this->handleError($e->getCode() . ': ' . $e->getMessage());
        }
    }

    private function handleError(string $msg, string $sql='') {
        $error_message = '== Mysql error: ' . $msg . $sql;
        debug_log($error_message);
        throw new RuntimeException($error_message);
    }

    /**
     * 
     * @param string $table - 레코드를 생성 할 테이블
     * @param array $record - 레코드의 필드(키)와 값을 연관 배열로 입력 받는다.
     */
    public function insert(string $table, array $record) {


        // 입력 받은 배열에서 필드와 값을 분리시켜 각각 $fields 와 $values 로 저장.
        $fields = [];
        $values = [];
        foreach($record as $k => $v) {
            $fields[] = $k;
            $values[] = $v;
        }

        // Statement 준비
        $stmt = $this->connection->stmt_init();

        // 입력 받은 테이블과 레코드 정보를 바탕으로 SQL 문장을 만든다.
        $sql = "INSERT INTO $table (".implode(',', $fields).") VALUES (".implode(",", array_fill(0, count($values), '?')).")";
        $re = $stmt->prepare($sql);
        if ( ! $re ) {
            $this->handleError($this->connection->error, $sql);
        }

        // 저장 할 값의 타입(형)을 계산
        $types = $this->types($values);

        // SQL 문장을 바탕으로 값의 타입(형)과 값을 바인드
        $stmt->bind_param($types, ...$values);

        // 쿼리
        $re = $stmt->execute();
        if ( ! $re ) {
            $this->handleError($this->connection->error, $sql);
        }
    }

    /**
     * @param $val
     * @return string
     */
    private function type(mixed $val): string {
        if ($val == '' || is_string($val)) return 's';
        if (is_float($val)) return 'd';
        if (is_int($val)) return 'i';
        return 'b';
    }

    /**
     * @param array $values
     * @return string
     */
    private function types(array $values): string {
        $type = '';
        foreach($values as $val) {
            $type .= $this->type($val);
        }
        return $type;
    }


}

$mysqli = new MyDB('mariadb', 'study', 'study', '12345a,*');

function db(): MyDB {
    global $mysqli;
    return $mysqli;
}