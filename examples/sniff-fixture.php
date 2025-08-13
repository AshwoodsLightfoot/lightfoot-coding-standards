<?php

declare(strict_types=1);

namespace LightfootCodingStandards\Examples;

// This file is a playground to run Lightfoot Coding Standard sniffs against.
// It intentionally includes both CORRECT and INCORRECT examples for:
// - LogicalOperatorLinePositionSniff (logical operators must start the next line in multi-line conditions)
// - ChainedMethodSemicolonPositionSniff (semicolon must be on its own line for multi-line chained calls)

final class Playground
{
    private object $deviceAssignment;
    private ?string $bodyTempFilePath = '0';
    private object $tempFileRepo;

    public function __construct()
    {
        // Dummy stubs to allow method chaining without runtime
        $this->deviceAssignment = new class {
            public function getInstallation(): object { return $this; }
            public function getOnce(): object { return $this; }
            public function getTwice(): object { return $this; }
            public function getThrice(): object { return $this; }
            public function getId(): int { return 123; }
        };
        $this->tempFileRepo = new class {
            public function create(string $p): void {}
        };
    }

    public function examples(): void
    {
        // Test variables used below (dummy values to prevent undefined variable warnings)
        $a = 1;
        $b = 2;
        $c = 3;

        // =============================
        // Logical operators (INCORRECT)
        // =============================
        if (
            $a &&
            $b
        ) {
            // violation at the '&&' ending a line
        }

        if (
            ($a || // inline comment after operator
            $b)
        ) {
            // violation at the '||' ending a line
        }

        if (
            $a === 1 &&
            $b === 2 &&
            $c === 3
        ) {
            // multiple '&&' violations
        }

        // Ternary mixed with logical operators (INCORRECT)
        $this->tempFileRepo->create(
            $this->bodyTempFilePath !== null &&
            $this->bodyTempFilePath !== '' &&
            $this->bodyTempFilePath !== '0' ? $this->bodyTempFilePath : ''
        );

        // ==========================
        // Logical operators (CORRECT)
        // ==========================
        if (
            $a
            && $b
        ) {
            // ok
        }

        if (
            ($a
            || $b)
        ) {
            // ok
        }

        if (
            $a === 1
            && $b === 2
            && $c === 3
        ) {
            // ok
        }

        $this->tempFileRepo->create(
            $this->bodyTempFilePath !== null
            && $this->bodyTempFilePath !== ''
            && $this->bodyTempFilePath !== '0' ? $this->bodyTempFilePath : ''
        );

        // Single-line conditions should be ignored (OK)
        if ($a && $b) {
            // ok single-line
        }

        // ==============================
        // Chained method semicolon (INCORRECT)
        // ==============================
        $installationId = $this->deviceAssignment
                        ->getInstallation()
                        ?->getId();

        $this->deviceAssignment
            ->getInstallation()
            ?->getId();

        $this->deviceAssignment
            ->getInstallation()
            ->getOnce()
            ->getTwice()
            ->getThrice()
            ->getId(); // semicolon on same line as final call

        // With different starting indent (INCORRECT)
        $this
            ->deviceAssignment
            ->getInstallation()
            ->getId();

        // ============================
        // Chained method semicolon (CORRECT)
        // ============================
        $installationId = $this->deviceAssignment
                        ->getInstallation()
                        ?->getId()
        ;

        $this->deviceAssignment
            ->getInstallation()
            ?->getId()
        ;

        $this->deviceAssignment
            ->getInstallation()
            ->getOnce()
            ->getTwice()
            ->getThrice()
            ->getId()
        ;

        $this
            ->deviceAssignment
            ->getInstallation()
            ->getId()
        ;

        // Edge cases for semicolon detection not to trigger (OK)
        $value = 1 + 2 + 3;
    }
}
