<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="tags")
 * @ORM\Entity(repositoryClass="App\Entity\TagRepository")
 */
class Tag
{

    /**
     * @ORM\Column(type="string", length=80)
     * @ORM\Id
     */
    private $id;

    /**
     * @ORM\ManyToMany(targetEntity="Post", mappedBy="tags")
     */
    private $postTags;
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->postTags = new ArrayCollection();
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Tag
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Add postTag
     *
     * @param \App\Entity\Post $postTag
     *
     * @return Tag
     */
    public function addPostTag(\App\Entity\Post $postTag)
    {
        $this->postTags[] = $postTag;

        return $this;
    }

    /**
     * Remove postTag
     *
     * @param \App\Entity\Post $postTag
     */
    public function removePostTag(\App\Entity\Post $postTag)
    {
        $this->postTags->removeElement($postTag);
    }

    /**
     * Get postTags
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPostTags()
    {
        return $this->postTags;
    }

    /**
     * Set id
     *
     * @param string $id
     *
     * @return Tag
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get id
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }
}
