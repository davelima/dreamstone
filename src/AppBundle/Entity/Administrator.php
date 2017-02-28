<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;

/**
 * @ORM\Table(name="administrators")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\AdministratorRepository")
 */
class Administrator implements AdvancedUserInterface, \Serializable
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;
    
    /**
     * @ORM\Column(type="string", length=25, unique=true)
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $email;
    
    /**
     * @ORM\Column(type="string", length=255)
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $roles;

    /**
     * @ORM\Column(name="is_active", type="boolean")
     */
    private $isActive;

    /**
     * @ORM\Column(type="string", length=36, nullable=true)
     */
    private $photo = 'no-photo.jpg';

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $facebookProfile;

    /**
     * @ORM\Column(type="string", length=128, nullable=true)
     */
    private $instagramProfile;

    /**
     * @ORM\Column(type="string", length=128, nullable=true)
     */
    private $twitterProfile;

    /**
     * @ORM\ManyToMany(targetEntity="Section", mappedBy="responsibles")
     */
    private $sectionResponsibles;

    private $plainPassword;

    public function __construct()
    {
        $this->isActive = true;
    }
    
    public function getUsername()
    {
        return $this->username;
    }
    
    public function getPassword()
    {
        return $this->password;
    }
    
    public function getSalt()
    {
        return null;
    }
    
    public function isAccountNonExpired()
    {
        return true;
    }

    public function isAccountNonLocked()
    {
        return true;
    }

    public function isCredentialsNonExpired()
    {
        return true;
    }

    public function isEnabled()
    {
        return $this->isActive;
    }

    public function serialize()
    {
        return serialize([
            $this->id,
            $this->username,
            $this->password,
            $this->isActive
        ]);
    }
    
    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->username,
            $this->password,
            $this->isActive
            ) = unserialize($serialized);
    }
    
    public function eraseCredentials()
    {}
    
    /**
     * Set plainPassword
     *
     * @param string $plainPassword
     *
     * @return User
     */
    public function setPlainPassword($plainPassword)
    {
        $this->plainPassword = $plainPassword;
    
        return $this;
    }
    
    /**
     * Get plainPassword
     *
     * @return boolean
     */
    public function getPlainPassword()
    {
        return $this->plainPassword;
    }
    

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
     * Set name
     *
     * @param string $name
     *
     * @return Administrator
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
     * Set username
     *
     * @param string $username
     *
     * @return Administrator
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return Administrator
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     *
     * @return Administrator
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * Get isActive
     *
     * @return boolean
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * Set password
     *
     * @param string $password
     *
     * @return Administrator
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }
    
    public function getMd5Email()
    {
        return md5($this->email);
    }

    /**
     * Set roles
     *
     * @param array $roles
     *
     * @return Administrator
     */
    public function setRoles(array $roles)
    {
        $this->roles = implode(',', $roles);

        return $this;
    }

    /**
     * Get roles
     *
     * @return array
     */
    public function getRoles()
    {
        return explode(',', $this->roles);
    }

    /**
     * Set photo
     *
     * @param string $photo
     *
     * @return Administrator
     */
    public function setPhoto($photo)
    {
        $this->photo = $photo;

        return $this;
    }

    /**
     * Get photo
     *
     * @return string
     */
    public function getPhoto()
    {
        return $this->photo;
    }

    /**
     * Add sectionResponsible
     *
     * @param \AppBundle\Entity\Section $sectionResponsible
     *
     * @return Administrator
     */
    public function addSectionResponsible(\AppBundle\Entity\Section $sectionResponsible)
    {
        $this->sectionResponsibles[] = $sectionResponsible;

        return $this;
    }

    /**
     * Remove sectionResponsible
     *
     * @param \AppBundle\Entity\Section $sectionResponsible
     */
    public function removeSectionResponsible(\AppBundle\Entity\Section $sectionResponsible)
    {
        $this->sectionResponsibles->removeElement($sectionResponsible);
    }

    /**
     * Get sectionResponsibles
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSectionResponsibles()
    {
        return $this->sectionResponsibles;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Administrator
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set facebookProfile
     *
     * @param string $facebookProfile
     *
     * @return Administrator
     */
    public function setFacebookProfile($facebookProfile)
    {
        $this->facebookProfile = $facebookProfile;

        return $this;
    }

    /**
     * Get facebookProfile
     *
     * @return string
     */
    public function getFacebookProfile()
    {
        return $this->facebookProfile;
    }

    /**
     * Set instagramProfile
     *
     * @param string $instagramProfile
     *
     * @return Administrator
     */
    public function setInstagramProfile($instagramProfile)
    {
        $this->instagramProfile = $instagramProfile;

        return $this;
    }

    /**
     * Get instagramProfile
     *
     * @return string
     */
    public function getInstagramProfile()
    {
        return $this->instagramProfile;
    }

    /**
     * Set twitterProfile
     *
     * @param string $twitterProfile
     *
     * @return Administrator
     */
    public function setTwitterProfile($twitterProfile)
    {
        $this->twitterProfile = $twitterProfile;

        return $this;
    }

    /**
     * Get twitterProfile
     *
     * @return string
     */
    public function getTwitterProfile()
    {
        return $this->twitterProfile;
    }
}
