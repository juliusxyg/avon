<?php
namespace Iqiyi\AvonBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Iqiyi\AvonBundle\Entity\AvonSubjectVote;

class UpdateRedeem
{
    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        $em = $args->getEntityManager();

        if ($entity instanceof AvonSubjectVote && $entity->getRedeemCode())
        {
            $code = $em->getRepository('IqiyiAvonBundle:AvonRedeemCode')
            					->findOneBy(array('code'=>$entity->getRedeemCode(), 'status'=>0));
            if($code){
            	$code->setStatus(1);
            	$em->persist($code);
            	$em->flush();
            }
        }
    }
}
?>