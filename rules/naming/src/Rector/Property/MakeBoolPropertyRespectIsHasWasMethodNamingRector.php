<?php

declare(strict_types=1);

namespace Rector\Naming\Rector\Property;

use Nette\Utils\Strings;
use PhpParser\Node;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Identifier;
use PhpParser\Node\Stmt\ClassLike;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Property;
use PhpParser\Node\Stmt\Return_;
use PhpParser\Node\VarLikeIdentifier;
use PHPStan\Type\BooleanType;
use Rector\BetterPhpDocParser\PhpDocInfo\PhpDocInfo;
use Rector\Core\Rector\AbstractRector;
use Rector\Core\RectorDefinition\CodeSample;
use Rector\Core\RectorDefinition\RectorDefinition;
use Rector\NodeTypeResolver\Node\AttributeKey;

/**
 * @see \Rector\Naming\Tests\Rector\Property\MakeBoolPropertyRespectIsHasWasMethodNamingRector\MakeBoolPropertyRespectIsHasWasMethodNamingRectorTest
 */
final class MakeBoolPropertyRespectIsHasWasMethodNamingRector extends AbstractRector
{
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

        $prefixedClassMethods = $this->getPrefixedClassMethods($node);
        if ($prefixedClassMethods === []) {
            return null;
        }

        $currentName = $this->getName($node);
        $expectedName = $this->resolveExpectedName($prefixedClassMethods, $currentName);
        if ($expectedName === null) {
            return null;
        }

        if ($currentName === $expectedName) {
            return null;
        }

        $this->renameProperty($node, $currentName, $expectedName);

        return $node;
    }

    private function shouldSkip(Property $property): bool
    {
        if (! $property->isPrivate()) {
            return true;
        }
        return ! $this->skipNonBooleanType($property);
    }

    /**
     * @return ClassMethod[]
     */
    private function getPrefixedClassMethods(Property $property): array
    {
        $name = $this->getName($property);
        if ($name === null) {
            return [];
        }

        $classLike = $property->getAttribute(AttributeKey::CLASS_NODE);
        if ($classLike === null) {
            return [];
        }

        $classMethods = $this->betterNodeFinder->findInstanceOf($classLike, ClassMethod::class);
        return array_filter($classMethods, function (ClassMethod $classMethod): bool {
            $classMethodName = $this->getName($classMethod);
            return Strings::match($classMethodName, '#^(is|was|has)[A-Z].+#') !== null;
        });
    }

    private function resolveExpectedName(array $classMethods, ?string $currentName): ?string
    {
        $classMethods = array_filter($classMethods, function (ClassMethod $classMethod) use ($currentName): bool {
            $stmts = $classMethod->stmts;
            if ($stmts === null) {
                return false;
            }

            if (! array_key_exists(0, $stmts)) {
                return false;
            }

            $return = $stmts[0];
            if (! $return instanceof Return_) {
                return false;
            }

            $node = $return->expr;
            if ($node === null) {
                return false;
            }

            return $this->isName($node, $currentName);
        });

        if (count($classMethods) !== 1) {
            return null;
        }

        $classMethod = reset($classMethods);
        return $this->getName($classMethod);
    }

    private function renameProperty(Property $property, string $currentName, string $expectedName): void
    {
        $classLike = $property->getAttribute(AttributeKey::CLASS_NODE);
        if ($classLike === null) {
            return;
        }

        $propertyProperty = $property->props[0];
        $propertyProperty->name = new VarLikeIdentifier($expectedName);
        $this->renamePropertyFetchesInClass($classLike, $currentName, $expectedName);
    }

    private function skipNonBooleanType(Property $property): bool
    {
        /** @var PhpDocInfo|null $phpDocInfo */
        $phpDocInfo = $property->getAttribute(AttributeKey::PHP_DOC_INFO);
        if ($phpDocInfo === null) {
            return false;
        }
        return $phpDocInfo->getVarType() instanceof BooleanType;
    }

    private function renamePropertyFetchesInClass(ClassLike $classLike, string $oldName, string $expectedName): void
    {
        // 1. replace property fetch rename in whole class
        $this->traverseNodesWithCallable([$classLike], function (Node $node) use (
            $oldName,
            $expectedName
        ): ?PropertyFetch {
            if (! $this->isLocalPropertyFetchNamed($node, $oldName)) {
                return null;
            }

            /** @var PropertyFetch $node */
            $node->name = new Identifier($expectedName);
            return $node;
        });
    }
}
