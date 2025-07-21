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
        $nextPtr = $phpcsFile->findNext(T_WHITESPACE, $stackPtr + 1, null, true);

        // Skip single-line or isolated conditions
        if (
            $tokens[$prevPtr]['line'] === $token['line']
            && $tokens[$nextPtr]['line'] === $token['line']
        ) {
            return;
        }

        // Operator must be at the start of the line
        if ($tokens[$prevPtr]['line'] === $token['line']) {
            $fix = $phpcsFile->addFixableError(
                sprintf('Logical operator "%s" should be at the start of a new line', $token['content']),
                $stackPtr,
                self::ERROR_CODE
            );

            if ($fix) {
                // Find the indentation of the line containing $nextPtr
                $nextLineStart = $phpcsFile->findFirstOnLine([], $nextPtr);
                $indentation = '';

                if ($nextLineStart !== false) {
                    // Get all whitespace from start of line until first non-whitespace
                    $i = $nextLineStart;
                    while (
                        $i < $nextPtr
                        && $tokens[$i]['code'] === T_WHITESPACE
                    ) {
                        $indentation .= $tokens[$i]['content'];
                        $i++;
                    }
                }

                $phpcsFile->fixer->beginChangeset();

                // Remove the operator from its current position
                $phpcsFile->fixer->replaceToken($stackPtr, '');

                // Find the end of the previous line
                $prevLineEnd = $phpcsFile->findEndOfStatement($prevPtr);

                // Add the operator at the start of a new line with proper indentation
                $phpcsFile->fixer->addContent(
                    $prevLineEnd,
                    $phpcsFile->eolChar . $indentation . $token['content'] . ' '
                );

                $phpcsFile->fixer->endChangeset();
            }
        }
    }
}
