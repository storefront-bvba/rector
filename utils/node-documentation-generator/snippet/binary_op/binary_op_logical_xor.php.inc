<?php

use PhpParser\Node\Expr\BinaryOp\LogicalXor;
use PhpParser\Node\Scalar\LNumber;

$left = new LNumber(5);
$right = new LNumber(10);

return new LogicalXor($left, $right);
