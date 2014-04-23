<?php
namespace Iqiyi\AvonBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

class Redeem extends Constraint
{
    public $message_bad = "天猫码不存在";

    public $message_used = "天猫码已使用";

    public function validatedBy()
	{
	    return 'iqiyi_avon.validator.redeem';
	}

	public function targets()
	{
		return self::PROPERTY_CONSTRAINT;
	}
}
?>