<?php

use PhpParser\Node\Expr\BinaryOp\ShiftRight;
use PhpParser\Node\Scalar\LNumber;

$left = new LNumber(5);
$right = new LNumber(10);

return new ShiftRight($left, $right);
