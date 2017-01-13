<?php
/**
 * @file 
 * @author: dabeen(lidianbin@baidu.com)
 * @date:   2017-01-04 01:09:19
 * @Last Modified by:   dabeen
 * @Last Modified time: 2017-01-04 10:39:26
 */

namespace Beenlee\SqlBuilder\Query; 

use Beenlee\SqlBuilder\Query\QueryAbstract; 

use Beenlee\SqlBuilder\Traits\UtilsTrait;
use Beenlee\SqlBuilder\Traits\FromTrait;
use Beenlee\SqlBuilder\Traits\WhereTrait;
use Beenlee\SqlBuilder\Traits\LimitTrait;

class Delete extends QueryAbstract {

    use FromTrait,
        WhereTrait,
        LimitTrait;

    protected $_delete = '';

    /**
     * 构造删除语句
     * @param string $table
     * @param $where array("[table] key"=>'value',"[table] key"=>'value')
     * @return SqlBuilder
     */
    public function delete($table, $where = null){

        $this->_delete = "DELETE ";
        if ($table) {
            $this->from($table);
        }
        if ($where) {
            $this->where($where);
        }

        return $this;
    }

    protected function combineSql() {
        $sqlArr = [];
        $sqlArr[] = $this->_delete;
        $sqlArr[] = $this->getFrom();
        $sqlArr[] = $this->getWhere();
        $sqlArr[] = $this->getLimit();

        return join(' ', $sqlArr);
    }
}