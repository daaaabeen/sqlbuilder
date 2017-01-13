<?php
/**
 * @file 
 * @author: dabeen(lidianbin@baidu.com)
 * @date:   2017-01-03 23:03:21
 * @Last Modified by:   dabeen
 * @Last Modified time: 2017-01-10 21:47:20
 */

namespace Beenlee\SqlBuilder\Traits;

// use Beenlee\SqlBuilder\Traits\UtilsTrait;

trait JoinTrait {

    // use UtilsTrait;

    protected $joins = array();

    /**
     * 左连接
     * 使用方法: 
     * leftJoin(
     *     'table [alias]',
     *     array('table key' => 'table|alias key'),
     *     array('table [key] [op]' => 'value')    
     * )
     * @param string $table 表名，支持别名
     * @param Array  $on    连接关系 k v 都是【表名 字段名]
     * @param Array  $where 筛选关系 左k 右值
     * @return SqlBuilder
     */
    public function leftJoin($table, $nickname = null) {

        return $this->_join('LEFT JOIN', $table, $nickname);
    }

    public function rightJoin($table, $nickname = null) {
        return $this->_join('RIGHT JOIN', $table, $nickname);
    }

    public function join($table, $nickname = null) {
        return $this->_join('JOIN', $table, $nickname);
    }

    protected function _join ($joinType, $table, $nickname = null) {

        if (is_subclass_of($table, '\Beenlee\SqlBuilder\Query\QueryAbstract')) {
            $table = "(" . $table->toSql() . ")";
        }
        if ($nickname) {
            $table = $table . ' AS ' . $nickname;
        }
        $this->joins[] = $joinType . " $table";
        return $this;
    }

    public function on ($on, $where = null) {
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
        $this->joins[] = "ON " . implode(' AND ', $temp);
        return $this;
    }

    public function getJoin() {
        if ($this->joins) {
            return join(' ', $this->joins);
        }
        return '';
    }
}