<?php
/**
 * @file 
 * @author: dabeen(lidianbin@baidu.com)
 * @date:   2017-01-04 00:06:47
 * @Last Modified by:   dabeen
 * @Last Modified time: 2017-01-10 21:22:39
 */

namespace Beenlee\SqlBuilder\Traits;

trait FromTrait {

    protected $_from = [];
    /**
     * 
     * @param string $val "table [nickname]"
     * @return SqlBuilder
     */
    public function from($val, $nickname = null) {

        if (is_subclass_of($val, '\Beenlee\SqlBuilder\Query\QueryAbstract')) {
            $val = "(" . $val->toSql() . ")";
        }

        if ($nickname) {
            $val = $val . ' AS ' . $nickname;
        }
        $this->_from[] = $val;

        return $this;
    }

    protected function getFrom() {
        return "FROM " . implode(',', $this->_from);
    }
}
