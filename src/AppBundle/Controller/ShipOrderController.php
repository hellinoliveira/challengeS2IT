<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Shiporder;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Controller\FOSRestController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Person;
use AppBundle\Form\PersonType;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

class ShipOrderController extends FOSRestController
{

    /**
     * This is the documentation of our API
     *
     * @ApiDoc(
     *  resource=true,
     *  description="Returns a collection of Shiporders Object",
     * )
     * @return Response
     * @View()
     * @Get("/api/shiporders")
     */
    public function getOrdersAction()
    {
        $orders = $this->getDoctrine()->getRepository("AppBundle:Shiporder")
            ->findAll();
        $view = $this->view($orders);

        return $this->handleView($view);
    }

    /**
     *
     * Get a single Shiporder info
     * @ApiDoc(
     *  description="Returns a Shiporder Object",
     *  requirements={
     *      {
     *          "name"="limit",
     *          "dataType"="integer",
     *          "requirement"="\d+",
     *          "description"="return a single shiporder"
     *      }
     *  },
     *  parameters={
     *      {"name"="id", "dataType"="integer", "required"=true, "description"="shiporder id"}
     *  },
     *  output={"collection"=false, "collectionName"="Shiporder", "class"="AppBundle\Entity\Shiporder"}
     * )
     * Get a person by ID
     * @param Shiporder $shiporder
     * @return Response
     *
     * @View()
     * @ParamConverter("shiporder", class="AppBundle:Shiporder")
     * @Get("/api/shiporder/{id}",)
     */
    public function getShiporderAction(Shiporder $shiporder)
    {
        $view = $this->view($shiporder);

        return $this->handleView($view);
    }
}
