<?php
/**
 * @file 
 * @author: dabeen(lidianbin@baidu.com)
 * @date:   2017-01-04 00:51:51
 * @Last Modified by:   dabeen
 * @Last Modified time: 2017-01-12 12:57:04
 */

namespace Beenlee\SqlBuilder\Query; 

use Beenlee\SqlBuilder\Query\QueryAbstract; 

use Beenlee\SqlBuilder\Traits\UtilsTrait;

class Insert extends QueryAbstract {

    use UtilsTrait;

    protected $_insert = '';

    /**
     * SQL组装-组装INSERT语句
     * 使用方法：$this->insert($table, $val)
     * @param string  $table 要插入的表的名字
     * @param Array  $val   一维或多维数组
     * @param boolean $only  是插入一条还是多条
     */
    public function insert($table, $val, $only = true) {
        if (!is_array($val) || empty($val)) return '';
        
        if ($only) {
            $keys = array_keys($val);
            $temp_k = '(' . $this->build_implode($keys, 1). ')';
            $temp_v = '(' . $this->build_implode($val). ')';
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
        $this->_insert = "INSERT INTO $table $temp_k  VALUES $temp_v";
        return $this;
    }

    public function combineSql() {
        $sqlArr = [];
        $sqlArr[] = $this->_insert;
        return join(' ', $sqlArr);
    }
}
