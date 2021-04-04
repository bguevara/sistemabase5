<?php

namespace App\Entity;

use App\Repository\UsersRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Entity(repositoryClass=UsersRepository::class)
 */
class Users implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $email;

        
    /**
     * @ORM\Column(type="string", length=150) 
     * @Assert\NotBlank(message="Campo nombres  no puede estar vacio.")
     */
    protected $name;


     /**
     * @ORM\Column(type="string", length=150) 
     * @Assert\NotBlank(message="Campo apellidos no puede estar vacio.")
     */
    protected $lastname;



    /**
     * @ORM\Column(type="string", length=150, nullable=true) 
     */
    protected $businessName;

    /**
     * @ORM\Column(type="text", nullable=true) 
     */
    protected $address;    

    /**
     * @ORM\Column(type="string", length=150, nullable=true) 
     */
    protected $Postalcode;

    /**
     * @ORM\Column(type="string", length=100, nullable=true) 
     * @Assert\NotBlank(message="Campo teléfono no puede estar vacio.")
     */
    protected $phone;

     /**
     * @ORM\Column(type="datetime") 
     */
    protected $lastConexion;    
    
    /**
     * @ORM\Column(type="boolean") 
     */
    protected $active;    
   

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     * @Assert\NotBlank(message="Campo no puede estar vacio.")
     * @Assert\Length(
     *      min = 6,
     *      max = 100,
     *      minMessage = "el mínimo de caracteres para la clave es de 6",
     *      maxMessage = "el maximo de caracteres para la clave es de 100"
     * )
     */
    private $password;


    

    public function __toString(): string
    {
        return $this->email;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    public function getRoles(): ?array
    {
        return $this->roles;
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getBusinessName(): ?string
    {
        return $this->businessName;
    }

    public function setBusinessName(?string $businessName): self
    {
        $this->businessName = $businessName;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getPostalcode(): ?string
    {
        return $this->Postalcode;
    }

    public function setPostalcode(?string $Postalcode): self
    {
        $this->Postalcode = $Postalcode;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getLastConexion(): ?\DateTimeInterface
    {
        return $this->lastConexion;
    }

    public function setLastConexion(\DateTimeInterface $lastConexion): self
    {
        $this->lastConexion = $lastConexion;

        return $this;
    }

    public function getActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;

        return $this;
    }
}
