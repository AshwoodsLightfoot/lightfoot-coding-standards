<?php

declare(strict_types=1);

namespace Lightfoot\LightfootCodingStandards\Sniffs\ControlStructures;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

/**
 * Enforces logical operators (&&, ||) to appear at the start of a new line in multi-line conditions.
 */
final class LogicalOperatorLinePositionSniff implements Sniff
{
    public const ERROR_CODE = 'LogicalOperatorStartOfLine';

    public function register(): array
    {
        return [
            T_BOOLEAN_AND,
            T_BOOLEAN_OR,
        ];
    }

    public function process(File $phpcsFile, $stackPtr): void
    {
        $tokens = $phpcsFile->getTokens();
        $token = $tokens[$stackPtr];

        $prevPtr = $phpcsFile->findPrevious(T_WHITESPACE, $stackPtr - 1, null, true);
        $nextPtr = $phpcsFile->findNext([T_WHITESPACE, T_COMMENT], $stackPtr + 1, null, true);

        // Skip single-line or isolated conditions
        if (
            $prevPtr !== false && $nextPtr !== false
            && $tokens[$prevPtr]['line'] === $token['line']
            && $tokens[$nextPtr]['line'] === $token['line']
        ) {
            return;
        }

        // Operator must be at the start of the line
        if ($prevPtr !== false && $nextPtr !== false && $tokens[$prevPtr]['line'] === $token['line']) {
            $fix = $phpcsFile->addFixableError(
                sprintf('Logical operator "%s" should be at the start of a new line', $token['content']),
                $stackPtr,
                self::ERROR_CODE
            );

            if ($fix) {
                $phpcsFile->fixer->beginChangeset();

                // Remove the operator from its current position
                $phpcsFile->fixer->replaceToken($stackPtr, '');

                // Insert operator at the start of the next line (after indentation),
                // right before the next non-whitespace token
                $phpcsFile->fixer->addContentBefore($nextPtr, $token['content'] . ' ');

                $phpcsFile->fixer->endChangeset();
            }
        }
    }
}
