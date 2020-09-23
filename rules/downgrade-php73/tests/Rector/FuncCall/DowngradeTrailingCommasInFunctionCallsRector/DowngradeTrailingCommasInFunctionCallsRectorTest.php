<?php

declare(strict_types=1);

namespace Rector\DowngradePhp73\Tests\Rector\FuncCall\DowngradeTrailingCommasInFunctionCallsRector;

use Iterator;
use Rector\Core\Testing\PHPUnit\AbstractRectorTestCase;
use Rector\Core\ValueObject\PhpVersionFeature;
use Rector\DowngradePhp73\Rector\FuncCall\DowngradeTrailingCommasInFunctionCallsRector;
use Symplify\SmartFileSystem\SmartFileInfo;

final class DowngradeTrailingCommasInFunctionCallsRectorTest extends AbstractRectorTestCase
{
    /**
     * @requires PHP >= 7.3
     * @dataProvider provideData()
     */
    public function test(SmartFileInfo $fileInfo): void
    {
        $this->doTestFileInfo($fileInfo);
    }

    public function provideData(): Iterator
    {
        return $this->yieldFilesFromDirectory(__DIR__ . '/Fixture');
    }

    protected function getRectorClass(): string
    {
        return DowngradeTrailingCommasInFunctionCallsRector::class;
    }

    protected function getPhpVersion(): string
    {
        return '7.2';
//        TODO: create constant
//        return PhpVersionFeature::;
    }
}
