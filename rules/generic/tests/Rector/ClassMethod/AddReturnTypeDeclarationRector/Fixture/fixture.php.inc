<?php

namespace Rector\Generic\Tests\Rector\Typehint\AddReturnTypeDeclarationRector\Fixture;

class SomeClass
{
    public function parse()
    {
    }

    public function resolve()
    {
    }

    public function nullable(): array
    {
    }
}

?>
-----
<?php

namespace Rector\Generic\Tests\Rector\Typehint\AddReturnTypeDeclarationRector\Fixture;

class SomeClass
{
    public function parse(): array
    {
    }

    public function resolve(): \SomeType
    {
    }

    public function nullable(): ?\SomeType
    {
    }
}

?>
