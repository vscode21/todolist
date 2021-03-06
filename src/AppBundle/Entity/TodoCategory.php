<?php

namespace AppBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;
use \Doctrine\Common\Collections\ArrayCollection;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity
 * @ORM\Table(name="todo_category")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TodoCategoryRepository")
 * @UniqueEntity("name") 
 */
class TodoCategory
{
    /**
    * @var int
    * 
    * @ORM\Column(type="integer")
    * @ORM\Id
    * @ORM\GeneratedValue(strategy="AUTO")
    */
    private $id;
    
    /**
    * @var string
    * 
    * @ORM\Column(type="string", length=255)
    */
    private $name;
    
    /**
     * @ORM\OneToMany(targetEntity="Todo", mappedBy="category")
     */
    private $todos;
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->todos = new ArrayCollection();
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
     * @return TodoCategory
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
     * Add todo
     *
     * @param \AppBundle\Entity\Todo $todo
     *
     * @return TodoCategory
     */
    public function addTodo(\AppBundle\Entity\Todo $todo)
    {
        $this->todos[] = $todo;

        return $this;
    }

    /**
     * Remove todo
     *
     * @param \AppBundle\Entity\Todo $todo
     */
    public function removeTodo(\AppBundle\Entity\Todo $todo)
    {
        $this->todos->removeElement($todo);
    }

    /**
     * Get todos
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTodos()
    {
        return $this->todos;
    }
}
