<?php

namespace App\Entity;

use App\Entity\User;
use App\Entity\Recipe;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CommentRepository::class)]
class Comment 
{  
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type:'integer')]
    private ?int $id = null;
    
    #[ORM\Column(type:'text')]
    #[assert\NotBlank()]
    private String $content;
    
    #[ORM\Column(type:'boolean')]
    private bool $isApproved = false;

    #[ORM\ManyToOne(targetEntity:User::class)]
    #[ORM\JoinColumn(nullable:false)]
    private User $author;
    
    #[ORM\ManyToOne(targetEntity:Recipe::class, inversedBy: 'comments')]
    #[ORM\JoinColumn(nullable:false)]
    private Recipe $recipe;
    
    #[ORM\Column()]
    private \DateTimeImmutable $createdAt;
    
    public function __construct(){
        $this->createdAt = new \DateTimeImmutable();
    } 
        
    /**
     * Get the value of id
     */ 
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the value of id
     *
     * @return  self
     */ 
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get the value of content
     */ 
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set the value of content
     *
     * @return  self
     */ 
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get the value of isApproved
     */ 
    public function getIsApproved()
    {
        return $this->isApproved;
    }

    /**
     * Set the value of isApproved
     *
     * @return  self
     */ 
    public function setIsApproved($isApproved)
    {
        $this->isApproved = $isApproved;

        return $this;
    }

    /**
     * Get the value of author
     */ 
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * Set the value of author
     *
     * @return  self
     */ 
    public function setAuthor($author)
    {
        $this->author = $author;

        return $this;
    }

    /**
     * Get the value of recipe
     */ 
    public function getRecipe()
    {
        return $this->recipe;
    }

    /**
     * Set the value of recipe
     *
     * @return  self
     */ 
    public function setRecipe($recipe)
    {
        $this->recipe = $recipe;

        return $this;
    }

    /**
     * Get the value of createdAt
     */ 
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set the value of createdAt
     *
     * @return  self
     */ 
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}