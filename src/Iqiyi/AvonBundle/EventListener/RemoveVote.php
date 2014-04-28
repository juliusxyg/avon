<?php
namespace Iqiyi\AvonBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Iqiyi\AvonBundle\Entity\AvonSubject;
use Iqiyi\AvonBundle\Entity\AvonPhoto;

class RemoveVote
{//为什么postRemove的话entity得不到subject id
    public function preRemove(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        $em = $args->getEntityManager();
        //删除subject的时候删除vote
        if ($entity instanceof AvonSubject && $entity->getSubjectId())
        {
            $query = $em->createQuery("DELETE FROM IqiyiAvonBundle:AvonSubjectVote u WHERE u.subjectId = " .$entity->getSubjectId());
            $query->execute();
        }

        if ($entity instanceof AvonPhoto && $entity->getPhotoId())
        {
            $query = $em->createQuery("DELETE FROM IqiyiAvonBundle:AvonPhotoVote u WHERE u.photoId = " .$entity->getPhotoId());
            $query->execute();
        }
    }
}
?>