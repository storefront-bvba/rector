<?php

namespace Rector\Naming\Tests\Rector\Property\MakeBoolPropertyRespectIsHasWasMethodNamingRector\Fixture;

class SkipNoDocblock
{
    private $full = false;

    public function isFull()
    {
        return $this->full;
    }
}

?>
