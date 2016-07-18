<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="todo")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TodoRepository")
 */
class Todo
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
     * @ORM\ManyToOne(targetEntity="TodoCategory", inversedBy="todos")
     * @ORM\JoinColumn(name="category", referencedColumnName="id")
     */
    private $category;
    
    /**
    * @var string
    * 
    * @ORM\Column(type="string", length=65534)
    */
    private $description;
    
    /**
     * @ORM\ManyToOne(targetEntity="TodoPriority", inversedBy="todos")
     * @ORM\JoinColumn(name="priority", referencedColumnName="id")
     */
    private $priority;
    
    /**
    * @var \DateTime
    * 
    * @ORM\Column(type="datetime")
    */
    private $date_created;
    
    /**
    * @var \DateTime
    * 
    * @ORM\Column(type="datetime")
    */
    private $date_due;

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
     * @return Todo
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
     * Set description
     *
     * @param string $description
     *
     * @return Todo
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
     * Set dateCreated
     *
     * @param \DateTime $dateCreated
     *
     * @return Todo
     */
    public function setDateCreated($dateCreated)
    {
        $this->date_created = $dateCreated;

        return $this;
    }

    /**
     * Get dateCreated
     *
     * @return \DateTime
     */
    public function getDateCreated()
    {
        return $this->date_created;
    }

    /**
     * Set dateDue
     *
     * @param \DateTime $dateDue
     *
     * @return Todo
     */
    public function setDateDue($dateDue)
    {
        $this->date_due = $dateDue;

        return $this;
    }

    /**
     * Get dateDue
     *
     * @return \DateTime
     */
    public function getDateDue()
    {
        return $this->date_due;
    }

    /**
     * Set category
     *
     * @param \AppBundle\Entity\TodoCategory $category
     *
     * @return Todo
     */
    public function setCategory(\AppBundle\Entity\TodoCategory $category = null)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category
     *
     * @return \AppBundle\Entity\TodoCategory
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Set priority
     *
     * @param \AppBundle\Entity\TodoPriority $priority
     *
     * @return Todo
     */
    public function setPriority(\AppBundle\Entity\TodoPriority $priority = null)
    {
        $this->priority = $priority;

        return $this;
    }

    /**
     * Get priority
     *
     * @return \AppBundle\Entity\TodoPriority
     */
    public function getPriority()
    {
        return $this->priority;
    }
}
