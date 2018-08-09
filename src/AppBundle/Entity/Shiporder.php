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


    public function __construct()
    {
        $this->items = new ArrayCollection();
    }
}