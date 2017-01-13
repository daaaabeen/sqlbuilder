<?php
/**
 * @file 
 * @author: dabeen(lidianbin@baidu.com)
 * @date:   2017-01-03 22:53:44
 * @Last Modified by:   dabeen
 * @Last Modified time: 2017-01-11 17:51:28
 */

namespace Beenlee\SqlBuilder\Query; 

use Beenlee\SqlBuilder\Query\QueryAbstract; 

use Beenlee\SqlBuilder\Traits\UtilsTrait;
use Beenlee\SqlBuilder\Traits\FromTrait;
use Beenlee\SqlBuilder\Traits\JoinTrait;
use Beenlee\SqlBuilder\Traits\GroupByTrait;
use Beenlee\SqlBuilder\Traits\WhereTrait;
use Beenlee\SqlBuilder\Traits\OrderByTrait;
use Beenlee\SqlBuilder\Traits\LimitTrait;

class Select extends QueryAbstract {

    use UtilsTrait,
        FromTrait,
        JoinTrait,
        GroupByTrait,
        WhereTrait,
        OrderByTrait,
        LimitTrait;

    protected $_select = '';

    /**
     * SQL组装-组装SELECT语句
     * 
     * @param $val array("table1"=>array("字段  [别名]","字段2"),"table2"=>array("colum1","colum2"),"table3"=>"*");
     * @return SqlBuilder
     */
    public function select($val, $mode = null) {
        $str = 'SELECT ';
        if (strtoupper($mode) === 'DISTINCT') {
            $str .= 'DISTINCT ';
        }

        $colums = [];

        if (empty($val)) {
            $val = '*';
        }

        if (!is_array($val)) {
            // $this->_select = $str . $val;
            $colums[] = $val;
        }
        else {
            foreach ($val as $k => $v) {
                $info = '';
                if (is_array($v) && !empty($v) ){
                    $i = 0;
                    do {
                        $arr = $this -> str2arr($v[$i]);
                        if ($i == 0){
                            $info = "{$k}.{$arr[0]}";
                            if(array_key_exists(1, $arr)){
                                $info .= ' AS '. $arr[1];
                            }
                            $colums[] = $info;
                        }
                        else{
                            $info = "{$k}.{$arr[0]}";
                            if(array_key_exists(1, $arr)){
                                $info .= ' AS '. $arr[1];
                            }
                            $colums[] = $info;
                        }
                        $i++;
                    }while ( $i < count($v));
                }
                else if(!empty($v)){
                    if (is_int($k)) {
                        $info = $v; 
                    }
                    else {
                        $info = "$k.$v";
                    }
                    $colums[] = $info;
                }
            }
        }

        $this->_select = $str . implode(', ', $colums);
        return $this;
    }

    protected function combineSql() {
        $sqlArr = [];
        $sqlArr[] = $this->_select;
        $sqlArr[] = $this->getFrom();
        $sqlArr[] = $this->getJoin();
        $sqlArr[] = $this->getWhere();
        $sqlArr[] = $this->getGroupBy();
        $sqlArr[] = $this->getOrderBy();
        $sqlArr[] = $this->getLimit();

        return join(' ', $sqlArr);
    }
}
