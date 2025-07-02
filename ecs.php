<?php declare(strict_types=1);

use Symplify\EasyCodingStandard\Config\ECSConfig;

return ECSConfig::configure()
    ->withPaths([
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ])
    ->withRootFiles()
    ->withRules([
        \PhpCsFixer\Fixer\Strict\DeclareStrictTypesFixer::class,
        \PhpCsFixer\Fixer\Import\NoUnusedImportsFixer::class,
    ])
    ->withPhpCsFixerSets(perCS20: true)
    ->withPreparedSets(psr12: true, common: true, symplify: true)
    ->withConfiguredRule(PhpCsFixer\Fixer\Phpdoc\PhpdocLineSpanFixer::class, [
        'property' => 'single',
        'method'   => 'single',
        'const'    => 'single',
    ])
    ->withConfiguredRule(PhpCsFixer\Fixer\Operator\BinaryOperatorSpacesFixer::class, [
        'operators' => [
            '=>' => 'align',
            '='  => 'align',
        ],
    ])
    ->withConfiguredRule(PhpCsFixer\Fixer\Phpdoc\PhpdocLineSpanFixer::class, [
        'const'    => 'single',
        'method'   => 'single',
        'property' => 'single',
    ])
    ->withConfiguredRule(Symplify\CodingStandard\Fixer\LineLength\LineLengthFixer::class, [
        'inline_short_lines' => false,
    ])
    ->withSkip([
        Symplify\CodingStandard\Fixer\ArrayNotation\ArrayOpenerAndCloserNewlineFixer::class,

        // <?php declare(strict_types=1); doesn't need to take up three or four lines:
        PhpCsFixer\Fixer\PhpTag\BlankLineAfterOpeningTagFixer::class,
        PhpCsFixer\Fixer\PhpTag\LinebreakAfterOpeningTagFixer::class,

        Symplify\CodingStandard\Fixer\ArrayNotation\StandaloneLineInMultilineArrayFixer::class,
        Symplify\CodingStandard\Fixer\ArrayNotation\ArrayListItemNewlineFixer::class,
    ])
;
