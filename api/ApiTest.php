<?php
/**
 * Created by PhpStorm.
 * User: Nyemo
 * Date: 19/01/2019
 * Time: 03:14
 */

namespace api;

//require_once 'ApiHeader.php';
use Queries\QueryBuilder;
class ApiTest extends \PHPUnit_Framework_TestCase
{
    public function testshouldGetRoutes()
    {
        /* $stack = [];
         $this->assertSame(0, count($stack));

         array_push($stack, 'foo');
         $this->assertSame('foo', $stack[count($stack)-1]);
         $this->assertSame(1, count($stack));

         $this->assertSame('foo', array_pop($stack));
         $this->assertSame(0, count($stack)); */

        $query = QueryBuilder::getInternalRoutes(0, 0, null, null, 0, 0);
        $this->assertSame(2, count($query));
        //$manager->getDataByQuery($query['sql'], $query['var']);
        //$this->assertSame(4, count($manager->managerOperationResult))  ;
    }
}
