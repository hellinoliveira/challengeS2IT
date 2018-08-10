<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @package AppBundle\Entity
 */
class Shiporder
{
    /**
     * @var int
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="Item", mappedBy="shiporder")
     */
    private $items;

    /**
     * @var \AppBundle\Entity\Shipto
     * @ORM\OneToOne(targetEntity="Shipto", mappedBy="shiporder")
     * @ORM\JoinColumn(name="shipto_id", referencedColumnName="id")
     */
    private $shipto;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    private $orderid;


    /**
     * @ORM\Column(type="datetime")
     * @var \DateTime
     */
    private $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->items = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return ArrayCollection
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * @param ArrayCollection $items
     * @return Shiporder
     */
    public function setItems($items)
    {
        $this->items = $items;
        return $this;
    }

    /**
     * @return Shipto
     */
    public function getShipto()
    {
        return $this->shipto;
    }

    /**
     * @param Shipto $shipto
     * @return Shiporder
     */
    public function setShipto($shipto)
    {
        $this->shipto = $shipto;
        return $this;
    }

    /**
     * @return int
     */
    public function getOrderid()
    {
        return $this->orderid;
    }

    /**
     * @param int $orderid
     * @return Shiporder
     */
    public function setOrderid($orderid)
    {
        $this->orderid = $orderid;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
     * @return Shiporder
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
        return $this;
    }


}