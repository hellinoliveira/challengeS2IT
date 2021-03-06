<?php

namespace AppBundle\Controller;

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

class PersonController extends FOSRestController
{

    /**
     * This is the documentation of our API
     *
     * @ApiDoc(
     *  resource=true,
     *  description="Returns a collection of People Object",
     * )
     * @return Response
     * @View()
     * @Get("/api/people")
     */
    public function getPeopleAction()
    {
        $people = $this->getDoctrine()->getRepository("AppBundle:Person")
            ->findAll();
        $view = $this->view($people);

        return $this->handleView($view);
    }

    /**
     * @ApiDoc(
     *  description="Returns a person Object",
     *  requirements={
     *      {
     *          "name"="limit",
     *          "dataType"="integer",
     *          "requirement"="\d+",
     *          "description"="return a single object"
     *      }
     *  },
     *  parameters={
     *      {"name"="id", "dataType"="integer", "required"=true, "description"="person id"}
     *  },
     *  output={"collection"=false, "collectionName"="Person", "class"="AppBundle\Entity\Person"}
     * )
     * Get a person by ID
     * @param Person $person
     * @return Response
     *
     * @View()
     * @ParamConverter("person", class="AppBundle:Person")
     * @Get("/api/person/{id}",)
     */
    public function getPersonAction(Person $person)
    {
        $view = $this->view($person);

        return $this->handleView($view);
    }
}
