<?php

namespace Iqiyi\AvonBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AvonPhotoVote
 */
class AvonPhotoVote
{
    /**
     * @var integer
     */
    private $photoVoteId;

    /**
     * @var integer
     */
    private $photoId;

    /**
     * @var string
     */
    private $voteIp;

    /**
     * @var integer
     */
    private $voteTime;


    /**
     * Get photoVoteId
     *
     * @return integer 
     */
    public function getPhotoVoteId()
    {
        return $this->photoVoteId;
    }

    /**
     * Set photoId
     *
     * @param integer $photoId
     * @return AvonPhotoVote
     */
    public function setPhotoId($photoId)
    {
        $this->photoId = $photoId;

        return $this;
    }

    /**
     * Get photoId
     *
     * @return integer 
     */
    public function getPhotoId()
    {
        return $this->photoId;
    }

    /**
     * Set voteIp
     *
     * @param string $voteIp
     * @return AvonPhotoVote
     */
    public function setVoteIp($voteIp)
    {
        $this->voteIp = $voteIp;

        return $this;
    }

    /**
     * Get voteIp
     *
     * @return string 
     */
    public function getVoteIp()
    {
        return $this->voteIp;
    }

    /**
     * Set voteTime
     *
     * @param integer $voteTime
     * @return AvonPhotoVote
     */
    public function setVoteTime($voteTime)
    {
        $this->voteTime = $voteTime;

        return $this;
    }

    /**
     * Get voteTime
     *
     * @return integer 
     */
    public function getVoteTime()
    {
        return $this->voteTime;
    }
}
