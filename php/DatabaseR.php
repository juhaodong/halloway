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
    'Address', 'EventName', 'EventSign', 'Type', 'Ginder', 'AvgCost', 'NeedPermission', 'ImagePath', 'EndTime',
    'Avaliable', 'CreaterContact', 'needContact', 'PersonLimit', 'PaymentMethod', 'Discription', 'PersonCondition');
$USER_INFO = array('Name', 'Role', 'Gender', 'Wechat', 'Phone', 'Email', 'QQ', 'WhatsApp');
$USER_EVENT_RELATION_MEMBER = 'Member';
$USER_EVENT_RELATION_CREATE = 'Create';
$USER_EVENT_RELATION_CHECKING = 'Checking';
$USER_EVENT_RELATION_WANT_IN = 'WantIn';

function get_event_member_detailed_info(mysqli $conn, $event_id)
{
    global $USER_EVENT_RELATION_MEMBER;
    global $USER_EVENT_RELATION_CREATE;
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
    return $members;
}

function get_user_event_relation(mysqli $conn, $event_id, $user_id)
{
    return (new SqlSelect($conn, array('Type'), 'Relation',
        array(sprintf("(EventID='%s')", $event_id),
            sprintf("(UserID='%s')", $user_id))))->execute_sql()[0]['Type'];

}

function get_full_event_info(mysqli $conn, $event_id)
{
    $event_info = (new SqlSelect($conn, array('*'), 'Event',
        array(sprintf("(EventID='%s')", $event_id), sprintf("(EndTime>NOW())"))))->execute_sql()[0];

    $members = get_event_member_detailed_info($conn, $event_id);

    $result = array('EventInfo' => $event_info, 'Members' => $members);
    return $result;
}

$q_parameter = $_GET['q'];
switch ($q_parameter) {

    case 'createEvent':
        $event_args_to_get = array_slice($EVENT_ARGS, 3);

        $creator_id = $_GET['CreaterID'];
        $role_id = (new SqlSelect($conn, array('Role'), 'User',
            array(sprintf("UserID='%s'", $creator_id))))->execute_sql()[0]['Role'];

        $event_limit = (new SqlSelect($conn, array('CreateEventLimit'), 'Role',
            array(sprintf("RoleID='%s'", $role_id))))->execute_sql()[0]['CreateEventLimit'];

        $event_count = (new SqlSelect($conn, array('COUNT(*)'), 'Relation',
            array(sprintf("(UserID='%s')", $creator_id),
                sprintf("(Type='%s')", $USER_EVENT_RELATION_CREATE))))->execute_sql()[0]['COUNT(*)'];

        if ((int)$event_count > (int)$event_limit) {
            echo sprintf('免费用户仅能创建%d个事件，付费请联系xxxxxxx', $event_limit);
            break;
        }

        $event_id_prefix = $_GET['Type'] . date('Ymd');
        $last_event_id = (new SqlSelect($conn, array('MAX(EventID)'), 'Event'))
            ->execute_sql()[0]['MAX(EventID)'];
        $event_id_ending = (substr($last_event_id, 0, 10) == $event_id_prefix)
            ? ((int)substr($last_event_id, -4) + 1) : 1;

        $event_args_to_use = array();
        foreach ($event_args_to_get as $event_arg) {
            $event_args_to_use[$event_arg] = sprintf("'%s'", $_GET[$event_arg]);
        }
        $event_args_to_use['EventID'] = sprintf("'%s%04d'", $event_id_prefix, $event_id_ending);
        $sql_insert = new SqlInsert($conn, 'Event', $event_args_to_use);
        echo common_execute_procedure($sql_insert, sprintf("%s%04d", $event_id_prefix, $event_id_ending));

        $sql_insert = new SqlInsert($conn, 'Relation', array(
            'EventID' => $event_args_to_use['EventID'],
            'UserID' => sprintf("'%s'", $creator_id),
            'Type' => sprintf("'%s'", $USER_EVENT_RELATION_CREATE),
            'ShowContact' => 1));
        common_execute_procedure($sql_insert, null);
        break;

    case 'joinEvent':
        $user_id = $_GET['UserID'];
        $event_id = $_GET['EventID'];
        $show_contact = $_GET['ShowContact'];
        $message = $_GET['Message'];

        $user_event_relation = get_user_event_relation($conn, $event_id, $user_id);

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

    case 'getEventInfo':
        $event_id = $_GET['EventID'];
        echo json_encode(get_full_event_info($conn, $event_id));
        break;

    case 'getAllEventInfo':
        $event_infos = (new SqlSelect($conn, array('*'), 'Event',
            array(sprintf("(EndTime>NOW())"))))->execute_sql();
        $results = array();
        foreach ($event_infos as $event_info) {
            $members = get_event_member_detailed_info($conn, $event_info['EventID']);
            $result = array('EventInfo' => $event_info, 'Members' => $members);
            array_push($results, $result);
        }

        echo json_encode($results);
        break;

    case 'getAllEventInfoWithoutMembers':
        $event_infos = (new SqlSelect($conn, array('*'), 'Event',
            array(sprintf("(EndTime>NOW())"))))->execute_sql();
        echo json_encode($event_infos);
        break;

    case 'getAllEventsRelated':
        $user_id = $_GET['UserID'];
        $result_events = array(array(), array(), array(), array());
        $user_relations = (new SqlSelect($conn, array('*'), 'Relation',
            array(sprintf("UserID='%s'", $user_id))))->execute_sql();
        foreach ($user_relations as $relation) {
            switch ($relation['Type']) {
                case  $USER_EVENT_RELATION_CREATE:
                    array_push($result_events[0], get_full_event_info($conn, $relation['EventID']));
                    $checking_relations = (new SqlSelect($conn, array('*'), 'Relation',
                        array(sprintf("Type='%s'", $USER_EVENT_RELATION_CHECKING),
                            sprintf("EventID='%s'", $relation['EventID']))))->execute_sql();
                    foreach ($checking_relations as $checking_relation) {
                        $event_info = get_full_event_info($conn, $checking_relation['EventID']);
                        $event_info['inUser'] = $checking_relation['UserID'];
                        array_push($result_events[3], $event_info);
                    }
                    break;
                case $USER_EVENT_RELATION_MEMBER:
                    array_push($result_events[1], get_full_event_info($conn, $relation['EventID']));
                    break;
                case $USER_EVENT_RELATION_CHECKING:
                    array_push($result_events[2], get_full_event_info($conn, $relation['EventID']));
                    break;
            }
        }
        echo json_encode($result_events);
        break;

    case 'admitMembership':
        $event_id = $_GET['EventID'];
        $user_id = $_GET['UserID'];

        $current_member_count = (new SqlSelect($conn, array('COUNT(*)'), 'Relation',
            array(sprintf("(EventID='%s')", $event_id),
                sprintf("(Type='%s')", $USER_EVENT_RELATION_MEMBER))))->execute_sql()[0]['COUNT(*)'];

        $member_limit = (new SqlSelect($conn, array('PersonLimit'), 'Event',
            array(sprintf("(EventID='%s')", $event_id))))->execute_sql()[0]['PersonLimit'];

        if ($current_member_count >= $member_limit) {
            echo '参加人数已满';
        } else {
            $sql_update = new SqlUpdate($conn, 'Relation',
                array('Type' => sprintf("'%s'", $USER_EVENT_RELATION_MEMBER)),
                array(sprintf("(EventID='%s')", $event_id), sprintf("(UserID='%s')", $user_id)));
            echo common_execute_procedure($sql_update);
        }
        break;

    case 'denyMembership':
        $event_id = $_GET['EventID'];
        $user_id = $_GET['UserID'];

        $sql_delete = new SqlDelete($conn, 'Relation',
            array(sprintf("(EventID='%s')", $event_id), sprintf("(UserID='%s')", $user_id)));
        echo common_execute_procedure($sql_delete);
        break;

    case 'quitEvent':
        $event_id = $_GET['EventID'];
        $user_id = $_GET['UserID'];

        $user_event_relation = get_user_event_relation($conn, $event_id, $user_id);

        if ($user_event_relation == $USER_EVENT_RELATION_MEMBER) {
            $sql_delete = new SqlDelete($conn, 'Relation',
                array(sprintf("(EventID='%s')", $event_id),
                    sprintf("(UserID='%s')", $user_id)));
            echo common_execute_procedure($sql_delete);
        } else {
            echo '错误，您不在本事件中';
        }
        break;

    case 'deleteEvent':
        $event_id = $_GET['EventID'];
        $user_id = $_GET['UserID'];

        $user_event_relation = get_user_event_relation($conn, $event_id, $user_id);
        if ($user_event_relation == $USER_EVENT_RELATION_CREATE) {
            $sql_delete = new SqlDelete($conn, 'Relation',
                array(sprintf("(EventID='%s')", $event_id)));
            echo common_execute_procedure($sql_delete, null);

            $sql_delete = new SqlDelete($conn, 'Event', array(sprintf("(EventID='%s')", $event_id)));
            echo common_execute_procedure($sql_delete);
        } else {
            echo '错误，您不是本事件的创建者';
        }
        break;

    case 'insertOrUpdateUser':
        $user_id = $_GET['UserID'];
        $user_exists = (new SqlSelect($conn, array('COUNT(*)'), 'User',
                array(sprintf("UserID='%s'", $user_id))))->execute_sql()[0]['COUNT(*)'] == 1;
        $kv_to_update_or_insert = array();
        $kv_to_update_or_insert['UserID'] = sprintf("'%s'", $user_id);
        foreach ($_GET as $key => $value) {
            if (in_array($key, $USER_INFO)) {
                $kv_to_update_or_insert[$key] = sprintf("'%s'", $value);
            }
        }

        if ($user_exists) {
            $sql_update = new SqlUpdate($conn, 'User', $kv_to_update_or_insert,
                array(sprintf("UserID='%s'", $user_id)));
            echo common_execute_procedure($sql_update);
        } else {
            $sql_insert = new SqlInsert($conn, 'User', $kv_to_update_or_insert);
            echo common_execute_procedure($sql_insert);
        }
        break;

    case 'searchEvent':
        $event_search_keys = array('keyword', 'type', 'spaceRemaining', 'city', 'country', 'endTime');
        $predicates = array();

        if ($_GET[$event_search_keys[0]]) {
            array_push($predicates, sprintf("(CONTAINS(EventName, '%s'))", $_GET[$event_search_keys[0]]));
        }

        if ($_GET[$event_search_keys[1]]) {
            array_push($predicates, sprintf("(Type='%s')", $_GET[$event_search_keys[1]]));
        }

        if ($_GET[$event_search_keys[2]]) {
            array_push($predicates,
                sprintf("(PersonLimit-(SELECT COUNT(*) FROM Relation WHERE Relation.EventID=Event.EventID)>%d)",
                    $_GET[$event_search_keys[2]]));
        }

        if ($_GET[$event_search_keys[3]]) {
            array_push($predicates, sprintf("(City='%s')", $_GET[$event_search_keys[3]]));
        }

        if ($_GET[$event_search_keys[4]]) {
            array_push($predicates, sprintf("(Country='%s')", $_GET[$event_search_keys[4]]));
        }

        if ($_GET[$event_search_keys[5]]) {
            array_push($predicates, sprintf("(EndTime>'%s')", $_GET[$event_search_keys[5]]));
        }

        $events = (new SqlSelect($conn, array('*'), 'Event', $predicates))->execute_sql();
        $results = array();
        foreach ($events as $event) {
            array_push($results, get_full_event_info($conn, $event['EventID']));
        }
        echo json_encode($results);
        break;

    case 'getMembers':
        $event_id = $_GET['EventID'];
        echo json_encode(get_full_event_info($conn, $event_id)['Members']);
        break;

    case 'getUserByUserID':
        $user_id = $_GET['UserID'];
        echo json_encode((new SqlSelect($conn, array('*'), 'User',
            array(sprintf("UserID='%s'", $user_id))))->execute_sql()[0]);
        break;

    default:
        echo 'no such method';
}