<?php

declare(strict_types=1);

// Incorrect: semicolon is on the same line as final chained method
$installationId = $this->deviceAssignment
                ->getInstallation()
                ?->getId();

// Correct: semicolon is on its own line aligned with start of statement
$installationId = $this->deviceAssignment
                ->getInstallation()
                ?->getId()
;

// Incorrect: semicolon is on the same line as final chained method
$this->deviceAssignment
    ->getInstallation()
    ?->getId();

// Correct: semicolon is on its own line aligned with start of statement
$this->deviceAssignment
    ->getInstallation()
    ?->getId()
;

// Incorrect: semicolon is on the same line as final chained method
$this->deviceAssignment
    ->getInstallation()
    ->getId();

// Correct: semicolon is on its own line aligned with start of statement
$this->deviceAssignment
    ->getInstallation()
    ->getId()
;

// Incorrect: semicolon is on the same line as final chained method
$this
    ->deviceAssignment
    ->getInstallation()
    ->getId();

// Correct: semicolon is on its own line aligned with start of statement
$this
    ->deviceAssignment
    ->getInstallation()
    ->getId()
;

// Incorrect: semicolon is on the same line as final chained method
$this
    ->deviceAssignment
    ->getInstallation()
    ->getOnce()
    ->getTwice()
    ->getThrice()
    ->getId();

// Correct: semicolon is on its own line aligned with start of statement
$this
    ->deviceAssignment
    ->getInstallation()
    ->getOnce()
    ->getTwice()
    ->getThrice()
    ->getId()
;
