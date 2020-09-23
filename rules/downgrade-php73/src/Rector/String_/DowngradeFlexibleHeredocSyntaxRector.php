<?php

declare(strict_types=1);

namespace Rector\DowngradePhp73\Rector\String_;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Scalar\String_;
use Rector\Core\Rector\AbstractRector;
use Rector\Core\RectorDefinition\CodeSample;
use Rector\Core\RectorDefinition\RectorDefinition;
use Rector\NodeTypeResolver\Node\AttributeKey;

/**
 * @see \Rector\DowngradePhp73\Tests\Rector\String_\DowngradeFlexibleHeredocSyntaxRector\DowngradeFlexibleHeredocSyntaxTest
 */
final class DowngradeFlexibleHeredocSyntaxRector extends AbstractRector
{
    public function getDefinition(): RectorDefinition
    {
        return new RectorDefinition('Changes heredoc/nowdoc that contains closing word to safe wrapper name', [
            new CodeSample(
                <<<'CODE_SAMPLE'
$query = <<<SQL
    SELECT *
    FROM `table`
    WHERE `column` = true;
    SQL;
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
$query = <<<SQL
SELECT *
FROM `table`
WHERE `column` = true;
SQL;
CODE_SAMPLE
            ),
        ]);
    }

    /**
     * @return string[]
     */
    public function getNodeTypes(): array
    {
        return [String_::class];
    }

    /**
     * @param String_ $node
     */
    public function refactor(Node $node): ?Node
    {
        if (! in_array($node->getAttribute(AttributeKey::KIND), [String_::KIND_HEREDOC, String_::KIND_NOWDOC], true)) {
            return null;
        }

        $node->setAttribute('docIndentation', '');
        if(($argNode = $node->getAttribute(AttributeKey::PARENT_NODE)) instanceof Arg){
            $methodNode = $argNode->getAttribute(AttributeKey::PARENT_NODE);
            $index = array_search($argNode,$methodNode->args);
            $methodNode->args[$index+1]=new Node\Stmt\Nop();
        }
        $node->setAttribute(AttributeKey::ORIGINAL_NODE, null);

        return $node;
    }
}
