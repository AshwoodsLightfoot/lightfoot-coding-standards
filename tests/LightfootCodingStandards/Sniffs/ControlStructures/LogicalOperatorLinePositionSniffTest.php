<?php

declare(strict_types=1);

namespace Test\LightfootCodingStandards\Sniffs\ControlStructures;

use PHPUnit\Framework\TestCase;
use SniffTestHelper;

class LogicalOperatorLinePositionSniffTest extends TestCase {
    public function testDisallowExtractSniff() {
        $fixtureFile = __DIR__ . '/fixture.php';
        $sniffFile = __DIR__ . '/../../../../src/LightfootCodingStandards/Sniffs/ControlStructures/LogicalOperatorLinePositionSniff.php';
        $helper = new SniffTestHelper();
        $phpcsFile = $helper->prepareLocalFileForSniffs($sniffFile, $fixtureFile);
        $phpcsFile->process();
        $lines = $helper->getErrorLineNumbersFromFile($phpcsFile);
        $this->assertEquals([8, 29, 37, 38], $lines);
    }
}
