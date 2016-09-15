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
     * @ORM\Column(name="super_user", type="boolean")
     */
    private $superUser;

    /**
     * @ORM\Column(name="is_active", type="boolean")
     */
    private $isActive;
    
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

    public function getRoles()
    {
        $roles = ['ROLE_ADMIN'];

        if ($this->superUser) {
            array_push($roles, 'ROLE_SUPER_ADMIN');
        }

        return $roles;
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
     * Set superUser
     *
     * @param boolean $superUser
     *
     * @return Administrator
     */
    public function setSuperUser($superUser)
    {
        $this->superUser = $superUser;

        return $this;
    }

    /**
     * Get superUser
     *
     * @return boolean
     */
    public function getSuperUser()
    {
        return $this->superUser;
    }
}
