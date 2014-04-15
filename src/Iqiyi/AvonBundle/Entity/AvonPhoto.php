<?php

namespace Iqiyi\AvonBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AvonPhoto
 */
class AvonPhoto
{
    /**
     * @var integer
     */
    private $photoId;

    /**
     * @var string
     */
    private $memName;

    /**
     * @var boolean
     */
    private $memGender;

    /**
     * @var string
     */
    private $memMobile;

    /**
     * @var string
     */
    private $photoUrl;

    /**
     * @var integer
     */
    private $addTime;


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
     * Set memName
     *
     * @param string $memName
     * @return AvonPhoto
     */
    public function setMemName($memName)
    {
        $this->memName = $memName;

        return $this;
    }

    /**
     * Get memName
     *
     * @return string 
     */
    public function getMemName()
    {
        return $this->memName;
    }

    /**
     * Set memGender
     *
     * @param boolean $memGender
     * @return AvonPhoto
     */
    public function setMemGender($memGender)
    {
        $this->memGender = $memGender;

        return $this;
    }

    /**
     * Get memGender
     *
     * @return boolean 
     */
    public function getMemGender()
    {
        return $this->memGender;
    }

    /**
     * Set memMobile
     *
     * @param string $memMobile
     * @return AvonPhoto
     */
    public function setMemMobile($memMobile)
    {
        $this->memMobile = $memMobile;

        return $this;
    }

    /**
     * Get memMobile
     *
     * @return string 
     */
    public function getMemMobile()
    {
        return $this->memMobile;
    }

    /**
     * Set photoUrl
     *
     * @param string $photoUrl
     * @return AvonPhoto
     */
    public function setPhotoUrl($photoUrl)
    {
        $this->photoUrl = $photoUrl;

        return $this;
    }

    /**
     * Get photoUrl
     *
     * @return string 
     */
    public function getPhotoUrl()
    {
        return $this->photoUrl;
    }

    /**
     * Set addTime
     *
     * @param integer $addTime
     * @return AvonPhoto
     */
    public function setAddTime($addTime)
    {
        $this->addTime = $addTime;

        return $this;
    }

    /**
     * Get addTime
     *
     * @return integer 
     */
    public function getAddTime()
    {
        return $this->addTime;
    }
}
