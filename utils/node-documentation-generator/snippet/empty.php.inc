<?php

use PhpParser\Node\Expr\Empty_;
use PhpParser\Node\Expr\Variable;

$variable = new Variable('variableName');

return new Empty_($variable);
