<?php
/**
 * @file 
 * @author: dabeen(lidianbin@baidu.com)
 * @date:   2017-01-03 23:25:45
 * @Last Modified by:   dabeen
 * @Last Modified time: 2017-01-04 10:40:16
 */
namespace Beenlee\SqlBuilder\Traits;

trait LimitTrait {

    protected $limit = '';

    /**
     * 
     * @param integer $start
     * @param integer $num
     * @return SqlBuilder
     */
    public function limit($start, $num=NULL){
        $start = (int) $start;
        $start = ($start < 0) ? 0 : $start;
        if ($num === NULL) {
            $this->limit = 'LIMIT ' . $start;
        } else {
            $num = abs((int) $num);
            $this->limit = 'LIMIT ' . $start .' ,'. $num;
        }
        return $this;
    }

    protected function getLimit() {
        return $this->limit;
    }

}
