<?php
/**
 * Created by PhpStorm.
 * User: uranu
 * Date: 2018/5/13
 * Time: 0:12
 */
$db_host = '5af756e2ece84.decdb.myqcloud.com';
//用户名
$db_user = 'root';
//密码
$db_password = 'Halloway_juhaodong';
//数据库名
$db_name = 'Record';
//端口
$db_port = '5014';
//连接数据库
$conn = new mysqli($db_host, $db_user, $db_password, $db_name, $db_port);// or die('连接数据库失败！');
//echo json_encode($conn).'<br/>';
if ($conn->connect_error) {
    die("连接失败: " . $conn->connect_error);
}

interface ISql
{
    public function get_sql();

    public function get_conn();

    public function execute_sql();
}

class SqlSelect implements ISql
{
    private $columns;
    private $table;
    private $predicates;
    private $conn;
    private $sql;

    public function __construct(mysqli $conn, array $columns, $table, array $predicates = null)
    {

        $this->conn = $conn;
        $this->columns = $columns;
        $this->table = $table;
        $this->predicates = $predicates;

        $this->sql =
            'SELECT ' . join(', ', $this->columns) .
            ' FROM ' . $this->table .
            ($this->predicates == null ? '' : ' WHERE (' . join(' AND ', $this->predicates) . ")") . ';';
    }

    /**
     * @return array
     */
    public function execute_sql()
    {
        // echo $this->sql;
        $query_result = $this->conn->query($this->sql);
        //echo $query_result->num_rows;
        $result = array();
        while ($row = $query_result->fetch_assoc()) {
            array_push($result, $row);
        }
        return $result;
    }

    public function get_sql()
    {
        return $this->sql;
    }

    public function get_conn()
    {
        return $this->conn;
    }
}

class SqlInsert implements ISql
{
    private $column_value_pairs;
    private $table;
    private $sql;
    private $conn;

    public function __construct(mysqli $conn, $table, array $column_value_pairs)
    {
        $this->conn = $conn;
        $this->column_value_pairs = $column_value_pairs;
        $this->table = $table;

        $this->sql =
            sprintf("INSERT INTO %s (%s) VALUES (%s)", $table,
                join(", ", array_keys($column_value_pairs)),
                join(", ", array_values($column_value_pairs)));

    }

    public function get_sql()
    {
        return $this->sql;
    }

    /**
     * @return bool|mysqli_result
     */
    public function execute_sql()
    {
        //echo $this->sql;
        $query_result = $this->conn->query($this->sql);
        return $query_result;
    }

    public function get_conn()
    {
        return $this->conn;
    }
}

class SqlUpdate implements ISql
{
    private $column_value_pairs;
    private $table;
    private $sql;
    private $conn;
    private $predicates;

    public function __construct(mysqli $conn, $table, array $column_value_pairs, array $predicates)
    {
        $this->conn = $conn;
        $this->column_value_pairs = $column_value_pairs;
        $this->table = $table;
        $this->predicates = $predicates;
        $sub_sentences = array();
        foreach ($column_value_pairs as $column => $value) {
            array_push($sub_sentences, ($column . "=" . $value));
        }

        $this->sql =
            "UPDATE " . $table .
            " SET " . join(", ", $sub_sentences) .
            " WHERE (" . join(" AND ", $predicates) . ");";
    }

    public function get_sql()
    {
        return $this->sql;
    }

    /**
     * @return bool|mysqli_result
     */
    public function execute_sql()
    {
        //echo $this->sql;
        $query_result = $this->conn->query($this->sql);
        return $query_result;
    }

    public function get_conn()
    {
        return $this->conn;
    }
}

class SqlDelete implements ISql
{
    private $table;
    private $sql;
    private $conn;
    private $predicates;

    public function __construct(mysqli $conn, $table, array $predicates)
    {
        $this->conn = $conn;
        $this->table = $table;
        $this->predicates = $predicates;

        $this->sql =
            "DELETE FROM " . $table .
            " WHERE (" . join(" AND ", $predicates) . ");";
    }

    public function get_sql()
    {
        return $this->sql;
    }

    /**
     * @return bool|mysqli_result
     */
    public function execute_sql()
    {
        //echo $this->sql;
        $query_result = $this->conn->query($this->sql);
        return $query_result;
    }

    public function get_conn()
    {
        return $this->conn;
    }
}

class SqlFreeQuery implements ISql
{
    private $sql;
    private $conn;

    public function __construct(mysqli $conn, $sql)
    {
        $this->conn = $conn;
        $this->sql = $sql;
    }

    public function get_sql()
    {
        return $this->sql;
    }

    /**
     * @return bool|mysqli_result
     */
    public function execute_sql()
    {
        //echo $this->sql;
        $query_result = $this->conn->query($this->sql);
        return $query_result;
    }

    public function get_conn()
    {
        return $this->conn;
    }
}

function common_execute_procedure(ISql $sql, $succeed = 'good', $failed = null)
{
    $result = $sql->execute_sql();
    if ($result === true) {
        return $succeed;
    } elseif ($failed) {
        return $failed;
    } else {
        return "Error on: " . $sql->get_sql() . "<br>" . $sql->get_conn()->error . "<br>";
    }
}



$q_parameter = $_GET['q'];
switch ($q_parameter) {
    case 'setLog':
        $sql_insert=new SqlInsert($conn,'Record',["StartTime"=>"'".date('Y-m-d H:i:s')."'"]);

        common_execute_procedure($sql_insert);
        echo json_encode((new SqlSelect($conn,array('MAX(idRecord)'),'Record'))->execute_sql());
        break;
    case 'finishLog':
        $kv=array("Do"=>"'".$_POST['Do']."'","EndTime"=>"'".date('Y-m-d H:i:s')."'");
        $sql_upd=new SqlUpdate($conn,"Record",$kv
           ,array(sprintf("(idRecord)='%s'", $_POST['idRecord'])));
        echo $sql_upd->get_sql();
        common_execute_procedure($sql_upd);
        break;
    case 'getAllRecord':
        echo json_encode((new SqlSelect($conn,array("*"),'Record'))->execute_sql());
        break;
    default:
        echo 'no such method';
}