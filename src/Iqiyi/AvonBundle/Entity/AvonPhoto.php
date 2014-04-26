<?php

namespace Iqiyi\AvonBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\HttpFoundation\File\UploadedFile;

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

//==================================================================

    private $file;
    private $temp;

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function preUpload()
    {
        if (null !== $this->getFile()) {
            // do whatever you want to generate a unique name
            $filename = sha1(uniqid(mt_rand(), true));
            $this->photoUrl = $filename.'.'.$this->getFile()->guessExtension();
        }
    }

    /**
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     */
    public function upload()
    {
        // the file property can be empty if the field is not required
        if (null === $this->getFile()) {
            return;
        }

        // if there is an error when moving the file, an exception will
        // be automatically thrown by move(). This will properly prevent
        // the entity from being persisted to the database on error
        $this->getFile()->move($this->getUploadRootDir(), $this->photoUrl);

        // check if we have an old image
        if (isset($this->temp)) {
            // delete the old image
            unlink($this->getUploadRootDir().'/'.$this->temp);
            // clear the temp image path
            $this->temp = null;
        }
        $this->file = null;
    }

    /**
     * @ORM\PostRemove()
     */
    public function removeUpload()
    {
        if ($file = $this->getAbsolutePath()) {
            unlink($file);
        }
    }

    /**
     * Sets file.
     *
     * @param UploadedFile $file
     */
    public function setFile(UploadedFile $file = null)
    {
        $this->file = $file;
        // check if we have an old image path
        if (isset($this->photoUrl)) {
            // store the old name to delete after the update
            $this->temp = $this->photoUrl;
            $this->photoUrl = null;
        } else {
            $this->photoUrl = 'initial';
        }
    }

    /**
     * Get file.
     *
     * @return UploadedFile
     */
    public function getFile()
    {
        return $this->file;
    }

    public function getAbsolutePath()
    {
        return null === $this->photoUrl
            ? null
            : $this->getUploadRootDir().'/'.$this->photoUrl;
    }

    public function getWebPath()
    {
        return null === $this->photoUrl
            ? null
            : $this->getUploadDir().'/'.$this->photoUrl;
    }

    protected function getUploadRootDir()
    {
        // the absolute directory path where uploaded
        // documents should be saved
        return __DIR__.'/../../../../web/'.$this->getUploadDir();
    }

    protected function getUploadDir()
    {
        // get rid of the __DIR__ so it doesn't screw up
        // when displaying uploaded doc/image in the view.
        return 'uploads/documents';
    }
//==================================================================


    /**
     * @var string
     */
    private $memAddress;

    /**
     * @var string
     */
    private $memZip;


    /**
     * Set memAddress
     *
     * @param string $memAddress
     * @return AvonPhoto
     */
    public function setMemAddress($memAddress)
    {
        $this->memAddress = $memAddress;

        return $this;
    }

    /**
     * Get memAddress
     *
     * @return string 
     */
    public function getMemAddress()
    {
        return $this->memAddress;
    }

    /**
     * Set memZip
     *
     * @param string $memZip
     * @return AvonPhoto
     */
    public function setMemZip($memZip)
    {
        $this->memZip = $memZip;

        return $this;
    }

    /**
     * Get memZip
     *
     * @return string 
     */
    public function getMemZip()
    {
        return $this->memZip;
    }
    /**
     * @var string
     */
    private $content;

    /**
     * @var boolean
     */
    private $status;


    /**
     * Set content
     *
     * @param string $content
     * @return AvonPhoto
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string 
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set status
     *
     * @param boolean $status
     * @return AvonPhoto
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return boolean 
     */
    public function getStatus()
    {
        return $this->status;
    }
    /**
     * @var integer
     */
    private $totalVote;


    /**
     * Set totalVote
     *
     * @param integer $totalVote
     * @return AvonPhoto
     */
    public function setTotalVote($totalVote)
    {
        $this->totalVote = $totalVote;

        return $this;
    }

    /**
     * Get totalVote
     *
     * @return integer 
     */
    public function getTotalVote()
    {
        return $this->totalVote;
    }
}
