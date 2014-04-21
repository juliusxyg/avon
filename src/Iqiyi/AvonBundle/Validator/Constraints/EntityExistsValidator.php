<?php
namespace Iqiyi\AvonBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Doctrine\ORM\EntityManager;

class EntityExistsValidator extends ConstraintValidator
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
        $entity = $this->entityManager->getRepository($constraint->entity)
						->findOneBy(array($constraint->property => $value));
		if($entity == null)
		{
			$this->context->addViolation($constraint->message);
			return;
		}
    }
}
?>