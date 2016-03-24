<?php

namespace XerkusTest\Neovim;

use PHPUnit_Framework_TestCase;

class NvimTest extends PHPUnit_Framework_TestCase
{
    public function testHasTreaded()
    {
        self::assertTrue(class_exists('Thread'));
    }
}
