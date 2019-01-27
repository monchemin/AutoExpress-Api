<?php

/**
 * Created by PhpStorm.
 * User: Nyemo
 * Date: 19/01/2019
 * Time: 02:57
 */
use Queries\QueryBuilder;
require_once '../api/ApiHeader.php';
class StackTest extends PHPUnit_Framework_TestCase
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
        $manager->getDataByQuery($query['sql'], $query['var']);
        $this->assertSame(4, count($manager->managerOperationResult))  ;
    }
}
