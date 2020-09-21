<?php

declare(strict_types=1);

namespace Rector\Naming\Rector\Property;

use PhpParser\Node;
use PhpParser\Node\Stmt\ClassLike;
use PhpParser\Node\Stmt\Property;
use Rector\Core\Rector\AbstractRector;
use Rector\Core\RectorDefinition\CodeSample;
use Rector\Core\RectorDefinition\RectorDefinition;
use Rector\Naming\Naming\ExpectedNameResolver;
use Rector\Naming\PropertyRenamer;
use Rector\Naming\ValueObject\PropertyRename;
use Rector\Naming\ValueObjectFactory\PropertyRenameFactory;
use Rector\NodeTypeResolver\Node\AttributeKey;

/**
 * @see \Rector\Naming\Tests\Rector\Property\MakeBoolPropertyRespectIsHasWasMethodNamingRector\MakeBoolPropertyRespectIsHasWasMethodNamingRectorTest
 */
final class MakeBoolPropertyRespectIsHasWasMethodNamingRector extends AbstractRector
{
    /**
     * @var PropertyRenamer
     */
    private $propertyRenamer;

    /**
     * @var ExpectedNameResolver
     */
    private $expectedNameResolver;

    /**
     * @var PropertyRenameFactory
     */
    private $propertyRenameFactory;

    public function __construct(
        PropertyRenamer $propertyRenamer,
        ExpectedNameResolver $expectedNameResolver,
        PropertyRenameFactory $propertyRenameFactory
    ) {
        $this->propertyRenamer = $propertyRenamer;
        $this->expectedNameResolver = $expectedNameResolver;
        $this->propertyRenameFactory = $propertyRenameFactory;
    }

    public function getDefinition(): RectorDefinition
    {
        return new RectorDefinition('Renames property to respect is/has/was method naming', [
            new CodeSample(
                <<<'CODE_SAMPLE'
class SomeClass
{
    private $full = false;

    public function isFull()
    {
        return $this->full;
    }
}
CODE_SAMPLE

                ,
                <<<'CODE_SAMPLE'
class SomeClass
{
    private $isFull = false;

    public function isFull()
    {
        return $this->isFull;
    }

}
CODE_SAMPLE

            ),
        ]);
    }

    /**
     * @return string[]
     */
    public function getNodeTypes(): array
    {
        return [Property::class];
    }

    /**
     * @param Property $node
     */
    public function refactor(Node $node): ?Node
    {
        if ($this->shouldSkip($node)) {
            return null;
        }

        $propertyRename = $this->propertyRenameFactory->create($node);
        if ($propertyRename === null) {
            return null;
        }

        if ($this->propertyRenamer->rename($propertyRename) === null) {
            return null;
        }

        return $node;
    }

    private function shouldSkip(Property $property): bool
    {
        if ($property->props[0]->default === null) {
            return true;
        }
        return ! ($this->isBooleanType($property) || $this->isBooleanType($property->props[0]->default));
    }
}
