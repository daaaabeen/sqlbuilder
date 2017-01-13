<?php
/**
 * @file 
 * @author: dabeen(lidianbin@baidu.com)
 * @date:   2017-01-03 23:31:33
 * @Last Modified by:   dabeen
 * @Last Modified time: 2017-01-04 11:29:59
 */

namespace Beenlee\SqlBuilder\Traits;
// use Beenlee\SqlBuilder\Traits\UtilsTrait;

trait OrderByTrait {

    // use UtilsTrait;

    protected $orderBy = [];

    /**
     * 排序s
     * @param Array $val 排序的规则 array("[table] key desc|asc")
     * @return SqlBuilder
     */
    public function orderBy($val){
        if (empty($val)) {
            return $this;
        }

        if (!is_array($val)) {
            $val = array($val);
        }

        foreach ($val as $v) {
            $arrTmp = $this->str2arr($v);
            $rule = array_pop($arrTmp);
            $this->orderBy[] = join('.', $arrTmp) . ' ' . strtoupper($rule);
        }

        return $this;
    }

    protected function getOrderBy() {
        if ($this->orderBy) {
            return 'ORDER BY ' . join(' , ', $this->orderBy);
        }
        return '';
    }
}
