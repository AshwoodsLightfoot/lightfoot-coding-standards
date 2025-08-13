<?php

declare(strict_types=1);

namespace Lightfoot\LightfootCodingStandards\Sniffs\ControlStructures;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

/**
 * Enforces that in multi-line chained calls, the terminating semicolon appears
 * on its own line, aligned with the start of the statement.
 */
final class ChainedMethodSemicolonPositionSniff implements Sniff
{
    public const ERROR_CODE = 'ChainedSemicolonOwnLine';

    public function register(): array
    {
        return [T_CLOSE_PARENTHESIS];
    }

    public function process(File $phpcsFile, $stackPtr): void
    {
        $tokens = $phpcsFile->getTokens();

        // Look ahead: if next non-whitespace token is a semicolon on the same line, candidate for violation
        $nextPtr = $phpcsFile->findNext(T_WHITESPACE, $stackPtr + 1, null, true);
        if ($nextPtr === false || $tokens[$nextPtr]['code'] !== T_SEMICOLON) {
            return;
        }
        // Only when semicolon is on the same line as the close parenthesis
        if ($tokens[$nextPtr]['line'] !== $tokens[$stackPtr]['line']) {
            return;
        }

        // Find the start of the statement for indentation and to verify multi-line
        if (method_exists($phpcsFile, 'findStartOfStatement')) {
            $startPtr = $phpcsFile->findStartOfStatement($stackPtr);
        } else {
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

        // Single-line statements are ignored
        if ($tokens[$startPtr]['line'] === $tokens[$stackPtr]['line']) {
            return;
        }

        // Determine indentation of the start-of-statement line
        $startLineFirstPtr = $phpcsFile->findFirstOnLine([], $startPtr);
        $indentation = '';
        if ($startLineFirstPtr !== false) {
            $x = $startLineFirstPtr;
            while ($x < $startPtr && $tokens[$x]['code'] === T_WHITESPACE) {
                $indentation .= $tokens[$x]['content'];
                $x++;
            }
        }

        $fix = $phpcsFile->addFixableError(
            'In multi-line chained calls, semicolon must be on its own line aligned with the start of the statement',
            $nextPtr,
            self::ERROR_CODE
        );

        if ($fix) {
            // Move semicolon to its own line aligned with start-of-statement
            $phpcsFile->fixer->beginChangeset();
            $phpcsFile->fixer->replaceToken($nextPtr, '');
            $phpcsFile->fixer->addContent($stackPtr, $phpcsFile->eolChar . $indentation . ';');
            $phpcsFile->fixer->endChangeset();
        }
    }
}
