<?php
namespace Iqiyi\AvonBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

class VoteLimit extends Constraint
{
    public $message_voted = "已经投过票";

    public $message_limited = "今天已经投过{{ limit }}票了，请明天再来";

    public $entity;

    public $property_subject;

    public $property_ip;

    public $limit;

    public function getRequiredOptions()
    {
        return array('entity', 'property_subject', 'property_ip', 'limit');
    }

    public function validatedBy()
	{
	    return 'iqiyi_avon.validator.vote_limit';
	}

	public function targets()
	{
		return self::PROPERTY_CONSTRAINT;
	}
}
?>