<?php

namespace LteAdmin\Tests;

class LteLoginTest extends LteTest
{
    public function test_example()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
