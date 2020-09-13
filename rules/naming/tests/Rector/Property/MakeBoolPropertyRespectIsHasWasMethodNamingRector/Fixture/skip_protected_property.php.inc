<?php

namespace Rector\Naming\Tests\Rector\Property\MakeBoolPropertyRespectIsHasWasMethodNamingRector\Fixture;

class SkipProtectedProperty
{
    /**
     * @var bool
     */
    public $full = false;

    public function isFull()
    {
        return $this->full;
    }
}

?>
