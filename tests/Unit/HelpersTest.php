<?php

namespace LteAdmin\Tests\Unit;

use Tests\TestCase;

/**
 * Class HelpersTest.
 * @package LteAdmin\Tests\Unit
 */
class HelpersTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testRelatedMethods()
    {
        $this->assertTrue(lte_related_methods('store') === ['store', 'create', 'access']);
        $this->assertTrue(lte_related_methods('update') === ['update', 'edit', 'access']);
        $this->assertTrue(lte_related_methods('create') === ['create', 'store', 'access']);
        $this->assertTrue(lte_related_methods('edit') === ['edit', 'update', 'access']);
        $this->assertTrue(lte_related_methods('destroy') === ['destroy', 'delete', 'access']);
        $this->assertTrue(lte_related_methods('delete') === ['delete', 'destroy', 'access']);
        $this->assertTrue(lte_related_methods('test') === ['test', 'access']);
    }
}
