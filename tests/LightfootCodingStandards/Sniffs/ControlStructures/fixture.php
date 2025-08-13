<?php

declare(strict_types=1);

// This fixture tests the LogicalOperatorLinePositionSniff
// The following line should trigger an error because the logical operator should be at the start of the next line
if (
    $condition1 &&
    $condition2
) {
    echo "This is a test";
}

// This is correct - logical operator at the start of the line
if (
    $condition1
    && $condition2
) {
    echo "This is correct";
}

// Single line conditions are ignored
if ($condition1 && $condition2) {
    echo "This is also correct";
}

// Testing with OR operator at the end of the line
if (
    $condition1 || // This would also trigger an error, but the test only checks line 7
    $condition2
) {
    echo "This is another test";
}

// '&&' should be at start of next line
$this->tempFileRepo->create(
    $this->bodyTempFilePath !== null &&
    $this->bodyTempFilePath !== '' &&
    $this->bodyTempFilePath !== '0' ? $this->bodyTempFilePath : ''
);
