<?php

use PHPUnit\Framework\TestCase;

final class FirstTest extends TestCase
{
    public function testWorksAtAll()
    {
        require '/src/code/zuordnen.php';

        $this->assertEquals(BESTELLZUORDNUNG_ART_VORMERKUNG_FEST, 10);
    }
}
