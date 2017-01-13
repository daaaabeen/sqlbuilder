<?php
/**
 * @file 
 * @author: dabeen(lidianbin@baidu.com)
 * @date:   2017-01-03 23:58:38
 * @Last Modified by:   dabeen
 * @Last Modified time: 2017-01-04 11:29:45
 */

namespace Beenlee\SqlBuilder\Traits;

// use Beenlee\SqlBuilder\Traits\UtilsTrait;

trait GroupByTrait {

    // use UtilsTrait;

    protected $_groupBy = [];
    protected $_having = [];

    /**
     * 分组
     * @param  $val array("[table] key","table2 key2")
     * @return SqlBuilder
     */
    public function groupBy($val){
        if (empty($val)) return $this;
        
        if (!is_array($val)) {
            $val = array ($val);
        }
        foreach ($val as $v){
            $arrTmp = $this->str2arr($v);
            $this->_groupBy[] = join('.', $arrTmp);
        }
        return $this;
    }

    /**
     * having筛选语句
     * operator : ['~', '!~', '=', '!=', '<', '>', '<=', '>=', '%', '!%']
     * 
     * @param array $val array('[table] key [operator]' => 'val'/array('1','2'))
     * @return SqlBuilder
     */
    public function having($val) {
        if (!is_array($val) || empty($val)) return $this;
        foreach ($val as $k => $v) {
            $this->_having[] = $this->build_kv($k, $v);
        }

        return $this;
    }

    protected function getGroupBy() {

        if ($this->_groupBy) {
            $sql = 'GROUP BY '. implode(',', $this->_groupBy);
            if ($this->_having) {
                $sql .= ' HAVING ' . implode(' AND ', $this->_having);
            }
            return $sql;
        }
        return '';
    }

}