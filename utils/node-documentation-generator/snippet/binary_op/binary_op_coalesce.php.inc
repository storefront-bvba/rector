<?php

use PhpParser\Node\Expr\BinaryOp\Coalesce;
use PhpParser\Node\Scalar\LNumber;

$left = new LNumber(5);
$right = new LNumber(10);

return new Coalesce($left, $right);
