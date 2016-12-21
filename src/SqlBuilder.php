<?php
/** 
 * Create On 2013-11-19 15:32:48
 * Author lidianbin
 * QQ: 281443751
 */

namespace Beenlee\SqlBuilder;

class SqlBuilder {

    protected $_sql = "";

    /**
     * 返回SQL
     * 
     * @return [type] [description]
     */
    public function getSql () {
        return $this->_sql;
    }

    public function setSql ($sql) {
        $this->_sql = $sql;
        return $this;
    }

    /**
     * SQL组装-组装INSERT语句
     * 使用方法：$this->insert($table, $val)
     * @param string  $table 要插入的表的名字
     * @param Array  $val   一维或多维数组
     * @param boolean $only  是插入一条还是多条
     */
    public function Insert($table, $val, $only = true) {
        if (!is_array($val) || empty($val)) return '';
        if ($only) {
            $temp_v = '(' . $this->build_implode($val). ')';
            $val = array_keys($val);
            $temp_k = '(' . $this->build_implode($val, 1). ')';
        }
        else {
            $keys = array_keys($val[0]);
            $temp_k = '(' . $this->build_implode($keys, 1). ')';
            $temp_v_arr = array();
            foreach ($val as $item) {
                $temp_v_arr[] = '(' . $this->build_implode($item). ')';
            }
            $temp_v = implode(',', $temp_v_arr);
        }
        $this->_sql = "INSERT INTO $table $temp_k  VALUES $temp_v";
        return $this;
    }

    /**
     * 构造删除语句
     * @param string $table
     * @param $where array("[table] key"=>'value',"[table] key"=>'value')
     * @return SqlBuilder
     */
    public function Delete($table, $where){
        if (empty($where)) {
            return;
        }
        $this->_sql = "DELETE FROM $table ";
        $this->Where($where);
        return $this;
    }

    /**
     * SQL组装-组装UPDATE语句
     * operator : ['+']
     * @param  array $val  array('key [operator]' => 'value')
     * @param $where array("[table] key [operator]"=>'value', "[table] key [operator]"=>'value')
     * @return SqlBuilder
     */
    public function Update($table, $val, $where) {
        if (!is_array($val) || empty($val)) {
            return;
        }
        $temp = array();
        foreach ($val as $k => $v) {
            $temp[] = $this->build_kv($k, $v);
        }
        $this -> _sql = "UPDATE $table  SET " . implode(',', $temp);
        $this -> Where(($where));
        return $this;
    }

    /**
     * SQL组装-组装SELECT语句
     * @param $val array("table1"=>array("字段  [别名]","字段2"),"table2"=>array("colum1","colum2"),"table3"=>"*");
     * @return SqlBuilder
     */
    public function Select ($val, $mode = null) {
        $str = 'SELECT ';
        if (strtoupper($mode) === 'DISTINCT') {
            $str .= 'DISTINCT ';
        }
        if (empty($val)) {
            $this -> _sql = $str . '* ';
        }
        if (!is_array($val)) {
            $this -> _sql = $str . $val . ' ';
            return $this;
        }
        else {
            $colums = array ();
            foreach ($val as $k => $v) {
                $info = '';
                if (is_array($v) && !empty($v) ){
                    $i = 0;
                    do {
                        $arr = $this -> str2arr($v[$i]);
                        if ($i == 0){
                            $info = "{$k}.{$arr[0]} ";
                            if(array_key_exists(1, $arr)){
                                $info .= ' AS '. $arr[1];
                            }
                            $colums[] = $info;
                        }
                        else{
                            $info = "{$k}.{$arr[0]} ";
                            if(array_key_exists(1, $arr)){
                                $info .= ' AS '. $arr[1];
                            }
                            $colums[] = $info;
                        }
                        $i++;
                    }while ( $i < count($v));
                }
                else if(!empty($v)){
                    if (is_int($k)) {
                        $info = $v; 
                    }
                    else {
                        $info = "$k.$v";    
                    }
                    $colums[] = $info;
                }
            }
        }
        $this -> _sql = $str . implode(',', $colums);
        return $this;
    }

    /**
     * 
     * @param string|Array $val array("table [nickname]" [,"table1"] )
     * @return SqlBuilder
     */
    public function From($val) {
        if (empty($val)) {
            return $this;
        }
        if (is_array($val)){
            $tables = $val;
        }
        else {
            $tables = array ($val);
        }
        $this->_sql .= " FROM " . implode(',', $tables);
        return $this;   
    }

    /**
     * 这个不建议使用
     * 使用下边那个吧
     * @param $val array("table 别名" => array("table colum" =>"table2 colum"),"table1"=>array( "table colum" =>"table2 colum" )));
     * @return SqlBuilder
     */
    public function Leftjoin($val){
        if(!is_array($val) || empty($val)){
            return $this;
        }
        $leftjoin = "";
        foreach ($val as $k => $v){
            $tableArr = $this->str2arr($k);
            
            list($key, $value) = each($v);
            
            $left = $this -> build_escape($key, 1);
            $right = $this -> build_escape($value, 1); 
            
            $table = $tableArr[0];
            if(array_key_exists(1, $tableArr)){
                $table .= ' AS '. $tableArr[1];
            }
            $leftjoin .= " LEFT JOIN $table ON $left = $right ";
        }
        $this->_sql .= $leftjoin;
        return $this;
    }

    /**
     * 新的左连接
     * 使用方法: 
     * LJoin(
     *     'table [alias]',
     *     array('table key' => 'table|alias key'),
     *     array('table [key] [op]' => 'value')    
     * )
     * @param string $table 表名，支持别名
     * @param Array  $on    连接关系 k v 都是【表名 字段名]
     * @param Array  $where 筛选关系 左k 右值
     * @return SqlBuilder
     */
    public function LJoin($table, $on, $where = null){
        if(!is_array($on) || empty($on)){
            return $this;
        }

        $tableArr = $this->str2arr($table);
        $table = implode('AS', $tableArr);
        
        $temp = array();
        foreach ($on as $k => $v){
            $left = $this -> build_escape($k, 1);
            $right = $this -> build_escape($v, 1);
            $temp[] = "$left = $right";
        }
        if (is_array($where) && !empty($where)) {
            foreach ($where as $k => $v) {
                $temp[] = $this->build_kv($k, $v);
            }
        }
        $this -> _sql .= " LEFT JOIN $table ON " . implode(' AND ', $temp);
        return $this;
    }


    /**
     * SQL组装-组装AND符号的WHERE语句
     * operator : ['~', '!~', '=', '!=', '<', '>', '<=', '>=', '%', '!%']
     * 
     * @param array $val array('[table] key [operator]' => 'val'/array('1','2'))
     * @return SqlBuilder
     */
    public function Where($val) {
        if (!is_array($val) || empty($val)) return $this;
        $temp = array();
        foreach ($val as $k => $v) {
            $temp[] = $this->build_kv($k, $v);
        }
    
        $this->_sql .= ' WHERE ' . implode(' AND ', $temp);
        return $this;
    }

    /**
     * SQL组装-组装KEY = VALUE形式
     * 返回：a = 'a'
     * build_kv($k, $v)
     * @param  string $k KEY值 '[table] key [operator]'
     * @param  string|Array $v VALUE值 'val'/array('1','2')
     * @return string
     */
    protected function build_kv($k, $v) {
        $operators = array('+', '-', '~', '!~', '=', '!=', '<', '>', '<=', '>=', '%', '!%');
        $operator = '=';
        $arr = array_filter(explode(' ', trim($k)), 'strlen');
        $len = count($arr);
        if ($len > 1) {
            $last = array_pop($arr);
            if (in_array($last, $operators)) {
                $k = join($arr, ' ');
                $operator = $last;
            }
        }

        if ($operator === '%') {
            return $this -> build_escape($k, 1) . " LIKE " . $this->build_escape("%$v%");
        }
        if ($operator === '!%') {
            return $this -> build_escape($k, 1) . " NOT LIKE " . $this->build_escape("%$v%");
        }
        if ($operator === '~') {
            return $this -> build_escape($k, 1) . " IS NULL ";
        }
        if ($operator === '!~') {
            return $this -> build_escape($k, 1) . " IS NOT NULL ";
        }
        if ($operator === '+' || $operator === '-') {
            $key = $this -> build_escape($k, 1);
            return $key . ' = ' . $key . $operator . (int) $v;
        }
        if (is_array($v)) {
            return $this -> build_escape($k, 1) . $this -> build_in($v, $operator);
        }
        return $this -> build_escape($k, 1) . " $operator " . $this->build_escape($v);
    }

    /**
     * 
     * @param  $val array("[table] key desc","table2 key2 asc")
     * @return SqlBuilder
     */
    public function Order($val){
        return $this -> OrderBy($val);
    }

    /**
     * 排序s
     * @param Array $val 排序的规则 array("[table] key desc|asc")
     * @return SqlBuilder
     */
    public function OrderBy($val){
        if (empty($val)) return $this;
        
        if (!is_array($val)) {
            $val = array ($val);
        }
        $orders = array();
        foreach ($val as $v){
            $arrTmp = $this->str2arr($v);
            $rule =  array_pop($arrTmp);
            $order[] = join('.', $arrTmp) . ' ' . strtoupper($rule);
        }
        $this->_sql .= ' ORDER BY '. implode(',', $order);
        return $this;
    }

    /**
     * 分组
     * @param  $val array("[table] key","table2 key2")
     * @return SqlBuilder
     */
    public function GroupBy($val){
        if (empty($val)) return $this;
        
        if (!is_array($val)) {
            $val = array ($val);
        }
        $rules = array();
        foreach ($val as $v){
            $arrTmp = $this->str2arr($v);
            // $rule =  array_pop($arrTmp);
            $rules[] = join('.', $arrTmp);
        }
        $this->_sql .= ' GROUP BY '. implode(',', $rules);
        return $this;
    }

    /**
     * having筛选语句
     * operator : ['~', '!~', '=', '!=', '<', '>', '<=', '>=', '%', '!%']
     * 
     * @param array $val array('[table] key [operator]' => 'val'/array('1','2'))
     * @return SqlBuilder
     */
    public function Having($val) {
        if (!is_array($val) || empty($val)) return $this;
        $temp = array();
        foreach ($val as $k => $v) {
            $temp[] = $this->build_kv($k, $v);
        }

        $this->_sql .= ' HAVING ' . implode(' AND ', $temp);
        return $this;
    }

    /**
     * 
     * @param unknown_type $start
     * @param unknown_type $num
     * @return SqlBuilder
     */
    public function Limit($start, $num=NULL){
        $start = (int) $start;
        $start = ($start < 0) ? 0 : $start;
        if ($num === NULL) {
            $this->_sql .= ' LIMIT ' . $start;
        } else {
            $num = abs((int) $num);
            $this->_sql .= ' LIMIT ' . $start .' ,'. $num;
        }
        
        return $this;   
    }

    /**
     * SQL组装-组装IN语句
     * 返回：('1','2','3')
     * 使用方法：$this->build_in($val)
     * @param  array $val 数组值  例如：array(1,2,3)
     * @return string
     */
    protected function build_in($val, $operator = '=') {
        $val = $this->build_implode($val);
        return $operator !== '!=' ? ' IN (' . $val . ')' : ' NOT IN (' . $val . ')';
    }

    /**
     * SQL组装-单个或数组参数过滤
     * @param  string|array $val
     * @param  int          $iskey 0-过滤value值，1-过滤字段
     * @return string|array
     */
    protected function build_escape($val, $iskey = 0) {
        if (is_array($val)) {
            foreach ($val as $k => $v) {
                $val[$k] = trim($this->build_escape_single($v, $iskey));
            }
            return $val;
        }
        return $this->build_escape_single($val, $iskey);
    }

    /**
     * SQL组装-私有SQL过滤
     *
     * @param  string $val 过滤的值
     * @param  int    $iskey 0-过滤value值，1-过滤字段
     * @return string
     */
    protected function build_escape_single($val, $iskey = 0) {
        if ($iskey === 0) {
            if (is_numeric($val)) {
                return " '" . $val . "' ";
            } else if( "NOW()" == strtoupper( trim($val) ) ) {
                return strtoupper( trim($val) );
            } else if( null === $val ){
                return "NULL";
            } else {
                return " '" . addslashes(stripslashes($val)) . "' ";
            }
        } else {
            $arr = $this->str2Arr($val);
            if(array_key_exists(1, $arr)){
                return " ".$arr[0]."."."".$arr[1]." ";
            }else{
                return " ".$arr[0]." ";
            }
        }
    }

    /**
     * SQL组装-将数组值通过，隔开
     * 返回：'1','2','3'
     * 使用方法：build_implode($val, $iskey = 0)
     * @param  array $val   值
     * @param  int   $iskey 0-过滤value值，1-过滤字段
     * @return string
     */
    protected function build_implode($val, $iskey = 0) {
        if (!is_array($val) || empty($val)) return '';
        return implode(',', $this->build_escape($val, $iskey));
    }

    /**
     * 将用空格隔开的字符串转成数组
     * @param unknown_type $str
     * @return array
     */
    protected function str2arr($str){
        $str = preg_replace('/\s(?=\s)/', '', trim($str) );
        return explode(" ", $str);
    }

}


/**
$sqlbuild = new SqlBuilder();
$sqlbuild->Select(array("table1"=>array("c1 aa","c3  sss","ss"),"table2"=>"*"))
        ->From(array("t1","t2"))
        ->Leftjoin(array("table1 id "=>"table2  id"))
        ->Where(array("ss sss"=>"1","sss  id"=>"2"))
        ->Order(array("aaa sss desc","sss  sss  asc"))
        ->Limit(0, 10);
echo $sqlbuild->getSql()."<br/>";
$sqlbuild->Insert("aaaa", array("sss"=>"NoW()","ss"=>null,"aaa"=>"ssssfddf"));
echo $sqlbuild->getSql()."<br/>";
$sqlbuild->Update("table",array("key"=>"11","key1"=>0,"key2"=>null,"key"=>"NOW()"),array("table key"=>"222"));
echo $sqlbuild->getSql()."<br/>";
$sqlbuild->Delete("table", array("table key"=>"11","table key1"=>0,"key2"=>null,"key"=>"NOW()") );
echo $sqlbuild->getSql()."<br/>";
*/
