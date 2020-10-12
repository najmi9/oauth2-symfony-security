<?php

namespace App\Security;

use Symfony\Component\Security\Core\User\UserInterface;

class User implements UserInterface
{
    private $username;
    private $avatar;

    private $roles = [];

    function __construct(array $data=[])
    {   
        if (isset($data['login'])) {
            $this->username = $data['login'];
            $this->avatar = $data['avatar_url'];
        }else{
            $this->username = $data['name'];
            $this->avatar = $data['picture'];   
        }   
    }
    
    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getAvatar()
    {
       return $this->avatar;
    }

    public function getUsername(): string
    {
        return (string) $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword()
    {
        // not needed for apps that do not check user passwords
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed for apps that do not check user passwords
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }
}
