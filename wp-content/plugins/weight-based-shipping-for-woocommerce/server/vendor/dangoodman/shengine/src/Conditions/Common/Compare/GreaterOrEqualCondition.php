<?php
namespace WbsVendors\Dgm\Shengine\Conditions\Common\Compare;


class GreaterOrEqualCondition extends \WbsVendors\Dgm\Shengine\Conditions\Common\Compare\CompareCondition
{
    public function isSatisfiedBy($value)
    {
        return $this->comparator->greater($value, $this->compareWith, true);
    }
}