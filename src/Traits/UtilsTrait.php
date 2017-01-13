<?php
/**
 * @file 
 * @author: dabeen(lidianbin@baidu.com)
 * @date:   2017-01-03 23:21:02
 * @Last Modified by:   dabeen
 * @Last Modified time: 2017-01-12 13:21:21
 */

namespace Beenlee\SqlBuilder\Traits;

trait UtilsTrait {

    /**
     * SQL组装-组装KEY = VALUE形式
     * 返回：a = 'a'
     * build_kv($k, $v)
     * @param  string $k KEY值 '[table] key [operator]'
     * @param  string|Array $v VALUE值 'val'/array('1','2')
     * @return string
     */
    protected function build_kv($k, $v) {
        $operators = array('+', '-', '~', '!~', '=', '!=', '<', '>', '<=', '>=', '%', '!%', '<>', '!<>');
        $operator = '=';
        $arr = array_filter(explode(' ', trim($k)), 'strlen');
        $len = count($arr);
        if ($len > 1) {
            $last = array_pop($arr);
            if (in_array($last, $operators)) {
                $k = join($arr, ' ');
                $operator = $last;
            }
        }

        $key = $this->build_escape($k, 1);
        $op = '';
        switch ($operator) {
            case '%':
                $op = 'LIKE';
                break;
            case '!%':
                $op = 'LIKE';
                break;
            case '~':
                $op = 'IS';
                break;
            case '!~':
                $op = 'IS NOT';
                break;
            case '<>': 
                $op = 'IN';
                break;
            case '!<>': 
                $op = 'NOT IN';
                break;
            case '+': 
                $op = '= ' . $key . ' +' ;
                break;
            case '-':
                $op = '= ' . $key . ' -';
                break;
            default:
                $op = $operator;
                break;
        }

        if (is_array($v)) {
            $val = '(' . $this->build_implode($v) . ')';
        }
        else if (is_subclass_of($v, '\Beenlee\SqlBuilder\Query\QueryAbstract')) {
            $val = "(" . $v->toSql() . ")";
        }
        else {
            if ($operator == '+' || $operator == '-') {
                $val = $v;
            }
            else {
                $val = $this->build_escape($v);
            }
        }
        return join(' ', [$key, $op, $val]);
    }

    /**
     * SQL组装-将数组值通过，隔开
     * 返回：'1','2','3'
     * 使用方法：build_implode($val, $iskey = 0)
     * @param  array $val   值
     * @param  int   $iskey 0-过滤value值，1-过滤字段
     * @return string
     */
    protected function build_implode($val, $iskey = 0) {
        if (!is_array($val) || empty($val)) return '';
        return implode(',', $this->build_escape($val, $iskey));
    }

    /**
     * SQL组装-单个或数组参数过滤
     * @param  string|array $val
     * @param  int          $iskey 0-过滤value值，1-过滤字段
     * @return string|array
     */
    protected function build_escape($val, $iskey = 0) {
        if (is_array($val)) {
            foreach ($val as $k => $v) {
                $val[$k] = trim($this->build_escape_single($v, $iskey));
            }
            return $val;
        }
        return $this->build_escape_single($val, $iskey);
    }

    /**
     * SQL组装-私有SQL过滤
     *
     * @param  string $val 过滤的值
     * @param  int    $iskey 0-过滤value值，1-过滤字段
     * @return string
     */
    protected function build_escape_single($val, $iskey = 0) {
        if ($iskey === 0) {
            if (is_subclass_of($val, '\Beenlee\SqlBuilder\Query\QueryAbstract')) {
                return "(" . $val->toSql() . ")";
            }
            else if (is_numeric($val)) {
                return "'" . $val . "'";
            } 
            else if( "NOW()" == strtoupper(trim($val) ) ) {
                return strtoupper( trim($val) );
            }
            else if( null === $val ){
                return "NULL";
            }
            else {
                return "'" . addslashes(stripslashes($val)) . "'";
            }
        }
        else {
            $arr = $this->str2Arr($val);
            if(array_key_exists(1, $arr)){
                return $arr[0]."."."".$arr[1];
            }else{
                return $arr[0];
            }
        }
    }

    /**
     * 将用空格隔开的字符串转成数组
     * @param unknown_type $str
     * @return array
     */
    protected function str2arr($str){
        $str = preg_replace('/\s(?=\s)/', '', trim($str) );
        return explode(" ", $str);
    }
}
