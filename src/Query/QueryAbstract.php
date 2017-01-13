<?php
/**
 * @file 
 * @author: dabeen(lidianbin@baidu.com)
 * @date:   2017-01-03 22:56:03
 * @Last Modified by:   dabeen
 * @Last Modified time: 2017-01-06 11:35:32
 */

namespace Beenlee\SqlBuilder\Query; 

abstract class QueryAbstract {

    protected $_sql = '';

    /**
     * combain SQL
     * 
     * @return [type] [description]
     */
    protected abstract function combineSql();

    public function toSql() {

        if (!$this->_sql) {
            $this->_sql = $this->combineSql();
        }

        return $this->_sql;
    }

}
