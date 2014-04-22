<?php
namespace Iqiyi\AvonBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\RequestStack;
/**
*  一个IP一天可以对一个SUBJECT投一票，总共可以投10个SUBJECT
*/
class VoteLimitValidator extends ConstraintValidator
{
	private $entityManager;
	private $request;

	public function __construct(EntityManager $entityManager, RequestStack $requestStack)
	{
		$this->entityManager = $entityManager;
		$this->request = $requestStack->getCurrentRequest();
	}

	/**
   * {@inheritDoc}
   */
  public function validate($value, Constraint $constraint)
  {
  	$ip = $this->request->getClientIp();
  	$today = mktime(0,0,0);
  	//check daily voted
    $query = $this->entityManager->getRepository($constraint->entity)
    		->createQueryBuilder('p')
		    ->where('p.'.$constraint->property_subject.' = :'.$constraint->property_subject.' AND p.'.$constraint->property_ip.' = :'.$constraint->property_ip.' AND p.voteTime > :today')
		    ->setParameters(array($constraint->property_subject => $value, $constraint->property_ip => $ip, 'today' => $today))
		    ->setMaxResults(1)
		    ->getQuery();

		$entities = $query->getResult();
		if(!empty($entities))
		{
			$this->context->addViolation($constraint->message_voted);
			return;
		}
		//check daily limit
		$query = $this->entityManager->getRepository($constraint->entity)
    		->createQueryBuilder('p')
    		->select('count(p.subjectVoteId)')
		    ->where('p.'.$constraint->property_ip.' = :'.$constraint->property_ip.' AND p.voteTime > :today')
		    ->setParameters(array($constraint->property_ip => $ip, 'today' => $today))
		    ->getQuery();

		$count = $query->getSingleScalarResult();
		if($count >= $constraint->limit)
		{
			$this->context->addViolation($constraint->message_limited, array("{{ limit }}" => $constraint->limit));
			return;
		}
  }
}
?>