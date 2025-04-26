<?php

use PHPUnit\Framework\TestCase;

class DatabaseTest extends TestCase
{
    /**
     * @requires PHP >= 8.1
     * @requires extension pdo
     * @requires extension pdo_mysql
     */
    public function testConMySQL()
    {
        $this->assertTrue(true);
    }
}
