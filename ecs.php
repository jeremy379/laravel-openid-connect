<?php

use PHP_CodeSniffer\Standards\Generic\Sniffs\Arrays\ArrayIndentSniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\Arrays\DisallowLongArraySyntaxSniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\Files\LineLengthSniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\Formatting\SpaceAfterCastSniff;
use PHP_CodeSniffer\Standards\PSR12\Sniffs\Files\FileHeaderSniff;
use PHP_CodeSniffer\Standards\Squiz\Sniffs\WhiteSpace\CastSpacingSniff;
use PHP_CodeSniffer\Standards\Squiz\Sniffs\WhiteSpace\FunctionSpacingSniff;
use PhpCsFixer\Fixer\Operator\BinaryOperatorSpacesFixer;
use SlevomatCodingStandard\Sniffs\Arrays\TrailingArrayCommaSniff;
use SlevomatCodingStandard\Sniffs\Classes\ClassConstantVisibilitySniff;
use SlevomatCodingStandard\Sniffs\Classes\ConstantSpacingSniff;
use SlevomatCodingStandard\Sniffs\Classes\EmptyLinesAroundClassBracesSniff;
use SlevomatCodingStandard\Sniffs\Classes\PropertySpacingSniff;
use SlevomatCodingStandard\Sniffs\Classes\TraitUseDeclarationSniff;
use SlevomatCodingStandard\Sniffs\Exceptions\DeadCatchSniff;
use SlevomatCodingStandard\Sniffs\Namespaces\AlphabeticallySortedUsesSniff;
use SlevomatCodingStandard\Sniffs\Namespaces\UnusedUsesSniff;
use SlevomatCodingStandard\Sniffs\Namespaces\UseFromSameNamespaceSniff;
use Symplify\EasyCodingStandard\Config\ECSConfig;
use Symplify\EasyCodingStandard\ValueObject\Set\SetList;

return ECSConfig::configure()
    ->withPaths([__DIR__])
    ->withSets([SetList::PSR_12])
    ->withRules([
        FileHeaderSniff::class,
        TraitUseDeclarationSniff::class,
        DisallowLongArraySyntaxSniff::class,
        UnusedUsesSniff::class,
        UseFromSameNamespaceSniff::class,
        DeadCatchSniff::class,
        AlphabeticallySortedUsesSniff::class,
        ClassConstantVisibilitySniff::class,
        TrailingArrayCommaSniff::class,
        ArrayIndentSniff::class,
        CastSpacingSniff::class,
        SpaceAfterCastSniff::class,
    ])
    ->withConfiguredRule(LineLengthSniff::class, [
        'absoluteLineLimit' => 150,
    ])
    ->withConfiguredRule(FunctionSpacingSniff::class, [
        'spacing' => 1,
        'spacingBeforeFirst' => 0,
        'spacingAfterLast' => 0,
    ])
    ->withConfiguredRule(PropertySpacingSniff::class, [
        'minLinesCountBeforeWithComment' => 1,
        'maxLinesCountBeforeWithComment' => 1,
        'minLinesCountBeforeWithoutComment' => 0,
        'maxLinesCountBeforeWithoutComment' => 1,
    ])
    ->withConfiguredRule(ConstantSpacingSniff::class, [
        'minLinesCountBeforeWithComment' => 1,
        'maxLinesCountBeforeWithComment' => 1,
        'minLinesCountBeforeWithoutComment' => 0,
        'maxLinesCountBeforeWithoutComment' => 1,
    ])
    ->withConfiguredRule(EmptyLinesAroundClassBracesSniff::class, [
        'linesCountAfterOpeningBrace' => 0,
        'linesCountBeforeClosingBrace' => 0,
    ])
    ->withConfiguredRule(BinaryOperatorSpacesFixer::class, [
        'default' => BinaryOperatorSpacesFixer::SINGLE_SPACE,
    ]);
