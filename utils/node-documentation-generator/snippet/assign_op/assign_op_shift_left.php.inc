<?php

use PhpParser\Node\Expr\AssignOp\ShiftLeft;
use PhpParser\Node\Scalar\LNumber;

$left = new LNumber(5);
$right = new LNumber(10);

return new ShiftLeft($left, $right);
