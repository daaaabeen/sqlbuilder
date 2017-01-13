<?php
/**
 * @file 
 * @author: dabeen(lidianbin@baidu.com)
 * @date:   2017-01-04 10:32:43
 * @Last Modified by:   dabeen
 * @Last Modified time: 2017-01-04 10:38:58
 */


namespace Beenlee\SqlBuilder\Query; 

use Beenlee\SqlBuilder\Query\QueryAbstract; 

use Beenlee\SqlBuilder\Traits\UtilsTrait;
use Beenlee\SqlBuilder\Traits\WhereTrait;

class Update extends QueryAbstract {

    use UtilsTrait, WhereTrait;

    protected $_update = '';

    /**
     * SQL组装-组装INSERT语句
     * 使用方法：$this->insert($table, $val)
     * @param string  $table 要插入的表的名字
     * @param Array  $val   一维或多维数组
     * @param boolean $only  是插入一条还是多条
     */
    public function update($table, $val, $where = null) {
        if (!is_array($val) || empty($val)) {
            return;
        }
        $temp = array();
        foreach ($val as $k => $v) {
            $temp[] = $this->build_kv($k, $v);
        }
        $this->_update = "UPDATE $table  SET " . implode(',', $temp);

        if ($where) {
            $this->Where(($where));
        }

        return $this;
    }

    public function combineSql() {
        $sqlArr = [];
        $sqlArr[] = $this->_update;
        $sqlArr[] = $this->getWhere();
        return join(' ', $sqlArr);
    }
}
