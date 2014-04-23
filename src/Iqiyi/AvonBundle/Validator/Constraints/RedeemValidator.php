<?php
namespace Iqiyi\AvonBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Doctrine\ORM\EntityManager;

class RedeemValidator extends ConstraintValidator
{
	private $entityManager;

	public function __construct(EntityManager $entityManager)
	{
		$this->entityManager = $entityManager;
	}

	/**
     * {@inheritDoc}
     */
    public function validate($value, Constraint $constraint)
    {
      $entity = $this->entityManager->getRepository('IqiyiAvonBundle:AvonRedeemCode')
      							->findOneBy(array('code'=>$value));
			if($entity == null)
			{
				$this->context->addViolation($constraint->message_bad);
				return;
			}
			elseif($entity->getStatus() == 1)
			{
				$this->context->addViolation($constraint->message_used);
				return;
			}
    }
}
?>