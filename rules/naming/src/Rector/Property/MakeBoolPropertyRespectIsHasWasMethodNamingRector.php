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

    public function __construct(PropertyRenamer $propertyRenamer, ExpectedNameResolver $expectedNameResolver)
    {
        $this->propertyRenamer = $propertyRenamer;
        $this->expectedNameResolver = $expectedNameResolver;
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

        ////////////////////////////////////////////////////////////////////////////////////////////////
        /// Factory start
        $currentName = $this->getName($node);
        $expectedName = $this->expectedNameResolver->resolveForProperty($node);
        if ($expectedName === null) {
            return null;
        }

        $objectType = $this->getObjectType($node);
        /** @var ClassLike $classLike */
        $classLike = $node->getAttribute(AttributeKey::CLASS_NODE);
        $propertyRename = new PropertyRename($node, $expectedName, $currentName, $objectType, $classLike);
        /// Factory finish
        /////////////////////////////////////////////////////////////////////////////////////////////////
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
