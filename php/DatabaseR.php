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
$db_name = 'Halloway';
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

        parent::__construct($this->sql);
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

$EVENT_ARGS = array('id', 'EventID', 'CreatTimeStamp', 'Country', 'City', 'CreaterID', 'Latitude', 'Longitude',
    'Address', 'EventName', 'EventSign', 'Type', 'Ginder', 'AvgCost', 'NeedPermission', 'ImagePath', 'EndTime', 'Avaliable',
    'CreaterContact', 'needContact');

$USER_EVENT_RELATION_MEMBER = 'member';
$USER_EVENT_RELATION_CREATE = 'create';
$USER_EVENT_RELATION_CHECKING = 'checking';
$USER_EVENT_RELATION_WANT_IN = 'wantIn';

$q_parameter = $_GET['q'];
switch ($q_parameter) {

    //post
    case 'createEvent':
        $event_args_to_get = array_slice($EVENT_ARGS, 3);

        $creator_id = $event_args_to_get['CreaterID'];

        $event_args_to_use = array();
        foreach ($event_args_to_get as $event_arg) {
            $event_args_to_use[$event_arg] = $_POST[$event_arg];
        }
        $sql_insert = new SqlInsert($conn, 'Event', $event_args_to_use);
        echo common_execute_procedure($sql_insert);
        break;

    //post
    case 'joinEvent':
        $user_id = $_POST['UserID'];
        $event_id = $_POST['EventID'];
        $show_contact = $_POST['showcontact'];
        $message = $_POST['Message'];

        $user_event_relation = (new SqlSelect($conn, array('Type'), 'Relation',
            array(sprintf("EventID='%s'", $event_id),
                sprintf("UserID='%s'", $user_id))))->execute_sql()[0]['Type'];

        switch ($user_event_relation) {
            case $USER_EVENT_RELATION_MEMBER:
                echo '已经在活动中了，无需参加';
                break;
            case $USER_EVENT_RELATION_CHECKING:
                echo '正在审核中，无需重复提交审核';
                break;
            case $USER_EVENT_RELATION_CREATE:
                echo '不可参加自己创建的事件';
                break;
            default:
                $event_need_permission = (new SqlSelect($conn, array('NeedPermission'), 'Event',
                    array(sprintf("EventID='%s'", $event_id))))->execute_sql()[0]['NeedPermission'];
                $relation_type = $event_need_permission ? $USER_EVENT_RELATION_CHECKING : $USER_EVENT_RELATION_MEMBER;

                $sql_insert = new SqlInsert($conn, 'Relation',
                    array('EventID' => sprintf("'%s'", $event_id),
                        'UserID' => sprintf("'%s'", $user_id),
                        'Type' => sprintf("'%s'", $relation_type),
                        'Message' => sprintf("'%s'", $message),
                        'ShowContact' => (int)$show_contact));
                echo common_execute_procedure($sql_insert, '参加成功');
        }

        break;

    //get
    case 'getEventInfo':
        $event_id = $_GET['EventID'];

        $event_info = (new SqlSelect($conn, array('*'), 'Event',
            array(sprintf("EventID='%s'", $event_id))))->execute_sql()[0];

        $member_event_info = (new SqlSelect($conn, array('*'), 'Relation',
            array(sprintf("(EventID='%s')", $event_id),
                sprintf("(Type='%s' OR Type='%s')", $USER_EVENT_RELATION_MEMBER, $USER_EVENT_RELATION_CREATE)
            )))->execute_sql();

        $members = array();
        foreach ($member_event_info as $member) {
            $member_info = (new SqlSelect($conn, array('*'), 'User',
                array(sprintf("UserID='%s'", $member['UserID']))))->execute_sql()[0];

            $member_info['ShowContact'] = $member['ShowContact'];
            $member_info['Message'] = $member['Message'];
            array_push($members, $member_info);
        }

        $result = array('EventInfo' => $event_info, 'Members' => $members);
        echo json_encode($result);
        break;
    default:
        echo 'no such method';
}