<?php

use PhpParser\Node\Expr\BinaryOp\Greater;
use PhpParser\Node\Scalar\LNumber;

$left = new LNumber(5);
$right = new LNumber(10);

return new Greater($left, $right);
