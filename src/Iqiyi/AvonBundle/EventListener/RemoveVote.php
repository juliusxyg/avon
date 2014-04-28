<?php
namespace Iqiyi\AvonBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Iqiyi\AvonBundle\Entity\AvonSubjectVote;

class RemoveVote
{
    public function postRemove(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        $em = $args->getEntityManager();

        //删除subject的时候删除vote
        if ($entity instanceof AvonSubject && $entity->getSubjectId())
        {
            $query = $em->createQuery("DELETE FROM IqiyiAvonBundile:AvonSubjectVote u WHERE u.suhjectId = " .$entity->getSubjectId());
            $query->execute();
        }

        if ($entity instanceof AvonPhoto && $entity->getPhotoId())
        {
            $query = $em->createQuery("DELETE FROM IqiyiAvonBundile:AvonPhotoVote u WHERE u.photoId = " .$entity->getPhotoId());
            $query->execute();
        }
    }
}
?>