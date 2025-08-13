<?php

declare(strict_types=1);

namespace Lightfoot\LightfootCodingStandards\Sniffs\Formatting;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

/**
 * Enforces that in multi-line chained calls, the terminating semicolon appears
 * on its own line, aligned with the start of the statement.
 *
 * Example (desired):
 * $installationId = $this->deviceAssignment
 *                 ->getInstallation()
 *                 ?->getId()
 * ;
 */
final class ChainedMethodSemicolonPositionSniff implements Sniff
{
    public const ERROR_CODE = 'ChainedSemicolonOwnLine';

    public function register(): array
    {
        return [T_SEMICOLON];
    }

    public function process(File $phpcsFile, $stackPtr): void
    {
        $tokens = $phpcsFile->getTokens();
        $semicolon = $tokens[$stackPtr];

        // Find the start of the statement (PHP_CodeSniffer helper) if available; fallback if not.
        if (method_exists($phpcsFile, 'findStartOfStatement')) {
            $startPtr = $phpcsFile->findStartOfStatement($stackPtr);
        } else {
            // Fallback: Walk back to the previous semicolon/open tag/brace as a heuristic.
            $startPtr = $stackPtr;
            while ($startPtr > 0) {
                $code = $tokens[$startPtr]['code'];
                if (in_array($code, [T_SEMICOLON, T_OPEN_TAG, T_OPEN_CURLY_BRACKET, T_COLON], true)) {
                    $startPtr++;
                    break;
                }
                $startPtr--;
            }
            if ($startPtr < 0) {
                $startPtr = 0;
            }
        }

        // Determine if semicolon is on the same line as previous token (candidate for violation)
        $prevPtr = $phpcsFile->findPrevious(T_WHITESPACE, $stackPtr - 1, null, true);
        if ($prevPtr === false) {
            return;
        }
        $sameLineAsPrev = ($tokens[$prevPtr]['line'] === $semicolon['line']);
        if (!$sameLineAsPrev) {
            return; // Semicolon already on its own line
        }

        // Ensure the statement is multi-line by checking there exists any token on a previous line before the semicolon
        $i = $prevPtr;
        while ($i > 0 && $tokens[$i]['line'] === $semicolon['line']) {
            $i--;
        }
        if ($i <= 0) {
            return; // No previous line content -> treat as single-line
        }


        // If the semicolon is already at the start of a new line (only whitespace before on this line), OK.
        $lineStart = $phpcsFile->findFirstOnLine([], $stackPtr);
        $onlyWhitespaceBefore = true;
        if ($lineStart !== false) {
            for ($k = $lineStart; $k < $stackPtr; $k++) {
                if ($tokens[$k]['code'] !== T_WHITESPACE) {
                    $onlyWhitespaceBefore = false;
                    break;
                }
            }
        }
        if ($onlyWhitespaceBefore) {
            return; // Already on its own line.
        }

        // Otherwise, add error and autofix: move semicolon to its own new line aligned with start of statement.
        $fix = $phpcsFile->addFixableError(
            'In multi-line chained calls, semicolon must be on its own line aligned with the start of the statement',
            $stackPtr,
            self::ERROR_CODE
        );

        if (!$fix) {
            return;
        }

        // Determine indentation of the start-of-statement line
        $startLineFirstPtr = $phpcsFile->findFirstOnLine([], $startPtr);
        $indentation = '';
        if ($startLineFirstPtr !== false) {
            $i = $startLineFirstPtr;
            while ($i < $startPtr && $tokens[$i]['code'] === T_WHITESPACE) {
                $indentation .= $tokens[$i]['content'];
                $i++;
            }
        }

        // Recompute previous non-whitespace before semicolon (may have been set earlier)
        $prevPtr = $phpcsFile->findPrevious(T_WHITESPACE, $stackPtr - 1, null, true);

        $phpcsFile->fixer->beginChangeset();
        // Remove the semicolon from the current position
        $phpcsFile->fixer->replaceToken($stackPtr, '');
        // Add a newline and the semicolon aligned with statement start after the previous token
        $phpcsFile->fixer->addContent($prevPtr, $phpcsFile->eolChar . $indentation . ';');
        $phpcsFile->fixer->endChangeset();
    }
}
