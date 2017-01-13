<?php
/**
 * @file 
 * @author: dabeen(lidianbin@baidu.com)
 * @date:   2017-01-10 18:04:19
 * @Last Modified by:   dabeen
 * @Last Modified time: 2017-01-12 13:22:15
 */

namespace Beenlee\SqlBuilder\Query; 

use Beenlee\SqlBuilder\Query\QueryAbstract;

class Now extends QueryAbstract {

    protected $_now = 'NOW()';

    public function now(){
        return $this;
    }

    protected function combineSql() {
        return $this->_now;
    }
}
