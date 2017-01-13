<?php
/** 
 * Create On 2013-11-19 15:32:48
 * Author lidianbin
 * QQ: 281443751
 */

namespace Beenlee\SqlBuilder;

use Beenlee\SqlBuilder\Query\Select;
use Beenlee\SqlBuilder\Query\Insert;
use Beenlee\SqlBuilder\Query\Update;
use Beenlee\SqlBuilder\Query\Delete;
use Beenlee\SqlBuilder\Query\NOW;


class SqlBuilder {

    protected $_currentQuery = null;

    /**
     * 返回SQL
     * 
     * @return string sql
     */
    public function getSql () {
        return $this->_currentQuery->toSql();
    }

    /**
     * SQL组装-组装INSERT语句
     * 使用方法：$this->insert($table, $val)
     * @param string  $table 要插入的表的名字
     * @param Array  $val   一维或多维数组
     * @param boolean $only  是插入一条还是多条
     */
    public function insert($table, $val, $only = true) {
        $this->_currentQuery = new Insert();
        return $this->_currentQuery->insert($table, $val, $only);
    }

    /**
     * 构造删除语句
     * @param string $table
     * @param $where array("[table] key"=>'value',"[table] key"=>'value')
     * @return SqlBuilder
     */
    public function delete($table, $where = null) {
        $this->_currentQuery = new Delete();
        return $this->_currentQuery->delete($table, $where);
    }

    /**
     * SQL组装-组装UPDATE语句
     * operator : ['+']
     * @param  array $val  array('key [operator]' => 'value')
     * @param $where array("[table] key [operator]"=>'value', "[table] key [operator]"=>'value')
     * @return SqlBuilder
     */
    public function update($table, $val, $where = null) {
        $this->_currentQuery = new Update();
        return $this->_currentQuery->update($table, $val, $where);
    }

    /**
     * SQL组装-组装SELECT语句
     * @param $val array("table1"=>array("字段  [别名]","字段2"),"table2"=>array("colum1","colum2"),"table3"=>"*");
     * @return SqlBuilder
     */
    public function select ($val, $mode = null) {
        $this->_currentQuery = new Select();
        return $this->_currentQuery->select ($val, $mode);
    }

    /**
     * 返回一个SQL查询函数Now()的对象
     * @return [type] [description]
     */
    public function now () {
        return new Now();
    }

}


/**
$sqlbuild = new SqlBuilder();
$sqlbuild->select(array("table1"=>array("c1 aa","c3  sss","ss"),"table2"=>"*"))
        ->from(array("t1","t2"))
        ->leftjoin(array("table1 id "=>"table2  id"))
        ->where(array("ss sss"=>"1","sss  id"=>"2"))
        ->order(array("aaa sss desc","sss  sss  asc"))
        ->limit(0, 10);
echo $sqlbuild->getSql()."<br/>";
$sqlbuild->insert("aaaa", array("sss"=>"NoW()","ss"=>null,"aaa"=>"ssssfddf"));
echo $sqlbuild->getSql()."<br/>";
$sqlbuild->update("table",array("key"=>"11","key1"=>0,"key2"=>null,"key"=>"NOW()"),array("table key"=>"222"));
echo $sqlbuild->getSql()."<br/>";
$sqlbuild->delete("table", array("table key"=>"11","table key1"=>0,"key2"=>null,"key"=>"NOW()") );
echo $sqlbuild->getSql()."<br/>";
*/
