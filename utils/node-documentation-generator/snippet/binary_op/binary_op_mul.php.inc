<?php

use PhpParser\Node\Expr\BinaryOp\Mul;
use PhpParser\Node\Scalar\LNumber;

$left = new LNumber(5);
$right = new LNumber(10);

return new Mul($left, $right);
