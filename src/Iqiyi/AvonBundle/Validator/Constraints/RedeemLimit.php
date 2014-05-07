<?php
namespace Iqiyi\AvonBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

class RedeemLimit extends Constraint
{
    public $message = "天猫码投票达到上限";

    public $entity;

    public $limit;

    public $property;

    public function validatedBy()
	{
	    return 'iqiyi_avon.validator.redeem_limit';
	}

	public function targets()
	{
		return self::PROPERTY_CONSTRAINT;
	}
}
?>