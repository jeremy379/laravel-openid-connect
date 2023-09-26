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
use SlevomatCodingStandard\Sniffs\Classes\ClassMemberSpacingSniff;
use SlevomatCodingStandard\Sniffs\Classes\ConstantSpacingSniff;
use SlevomatCodingStandard\Sniffs\Classes\EmptyLinesAroundClassBracesSniff;
use SlevomatCodingStandard\Sniffs\Classes\PropertySpacingSniff;
use SlevomatCodingStandard\Sniffs\Classes\TraitUseDeclarationSniff;
use SlevomatCodingStandard\Sniffs\Exceptions\DeadCatchSniff;
use SlevomatCodingStandard\Sniffs\Namespaces\AlphabeticallySortedUsesSniff;
use SlevomatCodingStandard\Sniffs\Namespaces\UnusedUsesSniff;
use SlevomatCodingStandard\Sniffs\Namespaces\UseFromSameNamespaceSniff;
use SlevomatCodingStandard\Sniffs\PHP\OptimizedFunctionsWithoutUnpackingSniff;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\EasyCodingStandard\ValueObject\Option;
use Symplify\EasyCodingStandard\ValueObject\Set\SetList;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set(FileHeaderSniff::class);
    $services->set(TraitUseDeclarationSniff::class);
    $services->set(DisallowLongArraySyntaxSniff::class);
    $services->set(UnusedUsesSniff::class);
    $services->set(UseFromSameNamespaceSniff::class);
    $services->set(OptimizedFunctionsWithoutUnpackingSniff::class);
    $services->set(DeadCatchSniff::class);
    $services->set(AlphabeticallySortedUsesSniff::class);
    $services->set(ClassConstantVisibilitySniff::class);
    $services->set(TrailingArrayCommaSniff::class);
    $services->set(ArrayIndentSniff::class);
    $services->set(ClassMemberSpacingSniff::class);
    $services->set(CastSpacingSniff::class);
    $services->set(SpaceAfterCastSniff::class);
    $services->set(LineLengthSniff::class)
        ->property('absoluteLineLimit', 120);
    $services->set(FunctionSpacingSniff::class)
        ->property('spacing', 1)
        ->property('spacingBeforeFirst', 0)
        ->property('spacingAfterLast', 0);
    $services->set(PropertySpacingSniff::class)
        ->property('minLinesCountBeforeWithComment', 1)
        ->property('maxLinesCountBeforeWithComment', 1)
        ->property('minLinesCountBeforeWithoutComment', 0)
        ->property('maxLinesCountBeforeWithoutComment', 1);
    $services->set(ConstantSpacingSniff::class)
        ->property('minLinesCountBeforeWithComment', 1)
        ->property('maxLinesCountBeforeWithComment', 1)
        ->property('minLinesCountBeforeWithoutComment', 0)
        ->property('maxLinesCountBeforeWithoutComment', 1);
    $services->set(EmptyLinesAroundClassBracesSniff::class)
        ->property('linesCountAfterOpeningBrace', 0)
        ->property('linesCountBeforeClosingBrace', 0);
    $services->set(BinaryOperatorSpacesFixer::class)
        ->call('configure', [
            ['default' => BinaryOperatorSpacesFixer::SINGLE_SPACE],
        ]);

    $parameters = $containerConfigurator->parameters();

    $parameters->set(Option::PATHS, [__DIR__]);
    $parameters->set(Option::SETS, [
        SetList::PSR_12,
    ]);
};
