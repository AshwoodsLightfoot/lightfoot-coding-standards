<?php

declare(strict_types=1);

namespace Test\LightfootCodingStandards\Sniffs\Formatting;

use PHPUnit\Framework\TestCase;
use SniffTestHelper;

class ChainedMethodSemicolonPositionSniffTest extends TestCase {
    public function testSemicolonMustBeOnNewLineForChainedCalls() {
        $fixtureFile = __DIR__ . '/fixture.php';
        $sniffFile = __DIR__ . '/../../../../src/LightfootCodingStandards/Sniffs/Formatting/ChainedMethodSemicolonPositionSniff.php';
        $helper = new SniffTestHelper();
        $phpcsFile = $helper->prepareLocalFileForSniffs($sniffFile, $fixtureFile);
        $phpcsFile->process();
        $lines = $helper->getErrorLineNumbersFromFile($phpcsFile);
        // Expect an error on the line where the semicolon is on same line as final chained method
        $this->assertEquals([8, 19, 30, 42, 58], $lines);
    }
}
