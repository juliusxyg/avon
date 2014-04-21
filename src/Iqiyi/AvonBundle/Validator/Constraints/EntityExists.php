<?php
namespace Iqiyi\AvonBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

class EntityExists extends Constraint
{
    public $message = "目标对象不存在";

    public $entity;

    public $property;

    public function getRequiredOptions()
    {
        return array('entity', 'property');
    }

    public function validatedBy()
	{
	    return 'iqiyi_avon.validator.entity_exists';
	}

	public function targets()
	{
		return self::PROPERTY_CONSTRAINT;
	}
}
?>