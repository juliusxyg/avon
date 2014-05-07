<?php
namespace Iqiyi\AvonBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Doctrine\ORM\EntityManager;

class RedeemLimitValidator extends ConstraintValidator
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
    	$today = mktime(0,0,0);
      	$query = $this->entityManager->getRepository($constraint->entity)
    		->createQueryBuilder('p')
    		->select('count(p.subjectVoteId)')
		    ->where('p.'.$constraint->property.' = :'.$constraint->property.' AND p.voteTime > :today AND p.voteType = 2')
		    ->setParameters(array($constraint->property => $value, 'today' => $today))
		    ->getQuery();

		$count = $query->getSingleScalarResult();
		if($count >= $constraint->limit)
		{
			$this->context->addViolation($constraint->message, array("{{ limit }}" => $constraint->limit));
			return;
		}
    }
}
?>