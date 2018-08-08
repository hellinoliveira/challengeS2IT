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

class PersonController extends FOSRestController
{

    /**
     * Get all the people
     * @return Response
     * @View()
     * @Get("/people")
     */
    public function getPeopleAction()
    {
        $people = $this->getDoctrine()->getRepository("AppBundle:Person")
            ->findAll();
        $view = $this->view($people);

        return $this->handleView($view);
    }

    /**
     * Get a person by ID
     * @param Person $person
     * @return Response
     *
     * @View()
     * @ParamConverter("person", class="AppBundle:Person")
     * @Get("/person/{id}",)
     */
    public function getPersonAction(Person $person)
    {
        $view = $this->view($person);

        return $this->handleView($view);
    }
}
