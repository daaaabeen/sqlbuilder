<?php
/**
 * @file 
 * @author: dabeen(lidianbin@baidu.com)
 * @date:   2017-01-03 23:42:28
 * @Last Modified by:   dabeen
 * @Last Modified time: 2017-01-04 11:30:07
 */

namespace Beenlee\SqlBuilder\Traits;

// use Beenlee\SqlBuilder\Traits\UtilsTrait;

trait WhereTrait {

    // use UtilsTrait;

    protected $wheres = [];

    /**
     * SQL组装-组装AND符号的WHERE语句
     * operator : ['~', '!~', '=', '!=', '<', '>', '<=', '>=', '%', '!%']
     * 
     * @param array $val array('[table] key [operator]' => 'val'/array('1','2'))
     * @return SqlBuilder
     */
    public function where($val) {
        if (!is_array($val) || empty($val)) return $this;

        foreach ($val as $k => $v) {
            $this->wheres[] = $this->build_kv($k, $v);
        }

        return $this;
    }

    protected function getWhere() {
        if ($this->wheres) {
            return 'WHERE ' . implode(' AND ', $this->wheres);
        }
        else {
            return '';
        }

    }

}