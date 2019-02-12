<?php

namespace Pintushi\Bundle\SecurityBundle\AccessRule\Expr;

use Pintushi\Bundle\SecurityBundle\AccessRule\Visitor;

/**
 * Represents access rule expression.
 */
interface ExpressionInterface
{
    /**
     * Visits the expression by visitor and generate proper visitor's expression.
     *
     * @param Visitor $visitor
     * @return mixed
     */
    public function visit(Visitor $visitor);
}
