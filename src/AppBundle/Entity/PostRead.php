<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="posts_reads")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\PostReadRepository")
 */
class PostRead
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Post")
     * @ORM\JoinColumn(name="post", referencedColumnName="id")
     */
    private $post;

    /**
     * @ORM\Column(type="datetime")
     */
    private $datetime;

    /**
     * @ORM\Column(type="string", length=15)
     */
    private $ip;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $userAgent;

    /**
     * @ORM\Column(type="text")
     */
    private $params;

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
     * Set datetime
     *
     * @param \DateTime $datetime
     *
     * @return PostRead
     */
    public function setDatetime($datetime)
    {
        $this->datetime = $datetime;

        return $this;
    }

    /**
     * Get datetime
     *
     * @return \DateTime
     */
    public function getDatetime()
    {
        return $this->datetime;
    }

    /**
     * Set ip
     *
     * @param string $ip
     *
     * @return PostRead
     */
    public function setIp($ip)
    {
        $this->ip = $ip;

        return $this;
    }

    /**
     * Get ip
     *
     * @return string
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * Set userAgent
     *
     * @param string $userAgent
     *
     * @return PostRead
     */
    public function setUserAgent($userAgent)
    {
        $this->userAgent = $userAgent;

        return $this;
    }

    /**
     * Get userAgent
     *
     * @return string
     */
    public function getUserAgent()
    {
        return $this->userAgent;
    }

    /**
     * Set params
     *
     * @param string $params
     *
     * @return PostRead
     */
    public function setParams($params)
    {
        $this->params = $params;

        return $this;
    }

    /**
     * Get params
     *
     * @return string
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * Set post
     *
     * @param \AppBundle\Entity\Post $post
     *
     * @return PostRead
     */
    public function setPost(\AppBundle\Entity\Post $post = null)
    {
        $this->post = $post;

        return $this;
    }

    /**
     * Get post
     *
     * @return \AppBundle\Entity\Post
     */
    public function getPost()
    {
        return $this->post;
    }
}
