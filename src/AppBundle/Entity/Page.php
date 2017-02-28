<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;

/**
 * @ORM\Table(name="pages")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\PageRepository")
 */
class Page
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $slug;

    /**
     * @ORM\Column(type="text")
     */
    private $content;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $sidebar;

    /**
     * @ORM\Column(type="boolean")
     */
    private $showOnMenu;

    /**
     * @ORM\Column(type="boolean")
     */
    private $status;

    /**
     * @ORM\Column(type="datetime")
     */
    private $creationDate;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $lastChange;

    /**
     * @ORM\ManyToOne(targetEntity="Administrator")
     * @ORM\JoinColumn(name="page_author", referencedColumnName="id")
     */
    private $author;

    /**
     * @ORM\Column(type="string", length=36)
     */
    private $featuredImage;

    /**
     * @ORM\Column(type="string", length=36)
     */
    private $shareImage;

    /**
     * @ORM\Column(type="string", length=7)
     */
    private $color;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return Page
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set slug
     *
     * @param string $slug
     *
     * @return Page
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set content
     *
     * @param string $content
     *
     * @return Page
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
     * Set sidebar
     *
     * @param string $sidebar
     *
     * @return Page
     */
    public function setSidebar($sidebar)
    {
        $this->sidebar = $sidebar;

        return $this;
    }

    /**
     * Get sidebar
     *
     * @return string
     */
    public function getSidebar()
    {
        return $this->sidebar;
    }

    /**
     * Set showOnMenu
     *
     * @param boolean $showOnMenu
     *
     * @return Page
     */
    public function setShowOnMenu($showOnMenu)
    {
        $this->showOnMenu = $showOnMenu;

        return $this;
    }

    /**
     * Get showOnMenu
     *
     * @return boolean
     */
    public function getShowOnMenu()
    {
        return $this->showOnMenu;
    }

    /**
     * Set status
     *
     * @param boolean $status
     *
     * @return Page
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
     * Set creationDate
     *
     * @param \DateTime $creationDate
     *
     * @return Page
     */
    public function setCreationDate($creationDate)
    {
        $this->creationDate = $creationDate;

        return $this;
    }

    /**
     * Get creationDate
     *
     * @return \DateTime
     */
    public function getCreationDate()
    {
        return $this->creationDate;
    }

    /**
     * Set lastChange
     *
     * @param \DateTime $lastChange
     *
     * @return Page
     */
    public function setLastChange($lastChange)
    {
        $this->lastChange = $lastChange;

        return $this;
    }

    /**
     * Get lastChange
     *
     * @return \DateTime
     */
    public function getLastChange()
    {
        return $this->lastChange;
    }

    /**
     * Set author
     *
     * @param \AppBundle\Entity\Administrator $author
     *
     * @return Page
     */
    public function setAuthor(\AppBundle\Entity\Administrator $author = null)
    {
        $this->author = $author;

        return $this;
    }

    /**
     * Get author
     *
     * @return \AppBundle\Entity\Administrator
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * Set featuredImage
     *
     * @param string $featuredImage
     *
     * @return Page
     */
    public function setFeaturedImage($featuredImage)
    {
        $this->featuredImage = $featuredImage;

        return $this;
    }

    /**
     * Get featuredImage
     *
     * @return string
     */
    public function getFeaturedImage()
    {
        return $this->featuredImage;
    }

    /**
     * Set shareImage
     *
     * @param string $shareImage
     *
     * @return Page
     */
    public function setShareImage($shareImage)
    {
        $this->shareImage = $shareImage;

        return $this;
    }

    /**
     * Get shareImage
     *
     * @return string
     */
    public function getShareImage()
    {
        return $this->shareImage;
    }

    /**
     * Set color
     *
     * @param string $color
     *
     * @return Page
     */
    public function setColor($color)
    {
        $this->color = $color;

        return $this;
    }

    /**
     * Get color
     *
     * @return string
     */
    public function getColor()
    {
        return $this->color;
    }
}
