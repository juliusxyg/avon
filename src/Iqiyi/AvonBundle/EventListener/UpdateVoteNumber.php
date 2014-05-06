<?php
namespace Iqiyi\AvonBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Iqiyi\AvonBundle\Entity\AvonSubjectVote;
use Iqiyi\AvonBundle\Entity\AvonPhotoVote;

class UpdateVoteNumber
{
    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        $em = $args->getEntityManager();
        //投票后更新counter, 赞+1 问题+2 天猫+50
        if ($entity instanceof AvonSubjectVote && $entity->getSubjectId())
        {
            $object = $em->getRepository('IqiyiAvonBundle:AvonSubject')
            					->find($entity->getSubjectId());
            if($object){
                $curVote = $object->getTotalVote();
                if($entity->getVoteType() == 2)
                    $curVote+=50;
                elseif($entity->getVoteType() == 1)
                    $curVote+=2;
                else
                    $curVote++;
            	$object->setTotalVote($curVote);
            	$em->persist($object);
            	$em->flush();
            }
        }
        if ($entity instanceof AvonPhotoVote && $entity->getPhotoId())
        {
            $object = $em->getRepository('IqiyiAvonBundle:AvonPhoto')
                                ->find($entity->getPhotoId());
            if($object){
                $curVote = $object->getTotalVote();
                $curVote++;
                $object->setTotalVote($curVote);
                $em->persist($object);
                $em->flush();
            }
        }
    }
}
?>