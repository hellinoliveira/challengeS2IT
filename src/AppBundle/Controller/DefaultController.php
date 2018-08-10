<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Item;
use AppBundle\Entity\Shiporder;
use AppBundle\Entity\Shipto;
use Doctrine\Common\Collections\ArrayCollection;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Phone;
use AppBundle\Entity\Person;

class DefaultController extends Controller
{
//    /**
//     * @Route("/", name="homepage")
//     */
//    public function indexAction(Request $request)
//    {
//        // replace this example code with whatever you need
//        return $this->render('default/index.html.twig', array(
//            'base_dir' => realpath($this->container->getParameter('kernel.root_dir') . '/..') . DIRECTORY_SEPARATOR,
//        ));
//    }


    /**
     * @Route("/", name="homepage")
     */
    public function indexAction()
    {
        $number = random_int(0, 100);

        return $this->render('default/upload.html.twig', array(
            'number' => $number,
        ));
    }

    /**
     * @Route("default/upload")
     * @Method({"POST"})
     */
    public function upload()
    {

        $files = array();
        $invalid = array();
        for ($i = 0; $i < count($_FILES['files']['name']); $i++) {
            if ($_FILES['files']['type'][$i] !== 'text/xml' && $_FILES['files']['type'][$i] !== 'application/xml') {
                $invalid[] = $_FILES['files']['name'][$i];
                continue;
            }
            $files[$_FILES['files']['name'][$i]] = [
                'tmp_name' => $_FILES['files']['tmp_name'][$i],
                'type' => $_FILES['files']['type'][$i]
            ];
        }
        ksort($files);
        $dir = $this->getParameter('upload_directory');
        $xml_paths = array();
        foreach ($files as $key => $value) {
            $path = $dir . basename($key);
            array_push($xml_paths, $path);
            if (!move_uploaded_file($value['tmp_name'], $path)) {
                $invalid[] = $key;
                continue;
            }
        }
        if (count($invalid) > 0) {
            $not_imported = implode(', ', $invalid);
            return view('index')
                ->with('type', 'warning')
                ->with('message', 'The following files could not be imported: ' . $not_imported);
        }
        foreach ($xml_paths as $file) {
            $xml = simplexml_load_file($file);
            $xml = json_decode(json_encode($xml), 1);
            if (array_key_exists('person', $xml)) {
                $this->savePersonInfo($xml['person']);
            } else {
                if (array_key_exists('shiporder', $xml)) {
                    $this->saveOrderInfo($xml['shiporder']);
                }
            }
        }
        return $this->render('default/upload.html.twig', array(
            'number' => '10',
        ));
    }

    private function savePersonInfo($xml)
    {
        foreach ($xml as $data) {
            $entityManager = $this->getDoctrine()->getManager();
            $person = $this->getDoctrine()
                ->getRepository(Person::class)
                ->findOneBy(array('personId' => $data['personid']));
            if (!empty($person)) {
                $entityManager->merge($person);
            } else {
                $person = new Person();
                $person->setPersonId($data['personid']);
                $person->setPersonName($data['personname']);
                $person->setPersonId($data['personid']);
                $entityManager->persist($person);
            }

            foreach ($data['phones'] as $phoneNumbers) {

                if (is_string($phoneNumbers)) {
                    $phoneNumbers = array(0 => $phoneNumbers);
                }
                foreach ($phoneNumbers as $phoneNumber) {
                    $phone = new Phone();
                    $phone->setPerson($person);
                    $phone->setPhone($phoneNumber);

                    $entityManager = $this->getDoctrine()->getManager();
                    $phoneExists = $this->getDoctrine()
                        ->getRepository(Phone::class)
                        ->findOneBy(array('phone' => $phoneNumber, 'person' => $person));
                    if (!$phoneExists) {
                        $person->getPhones()->add($phone);
                    }
                }
            }
            $entityManager->merge($person);
            $entityManager->flush();
        }
    }

    private function saveOrderInfo($xml)
    {
        foreach ($xml as $data) {

            $entityManager = $this->getDoctrine()->getManager();
            $shiporder = $this->getDoctrine()
                ->getRepository(Shiporder::class)
                ->findOneBy(array('orderid' => $data['orderid']));
            if (!empty($shiporder)) {
                $entityManager->merge($shiporder);
            } else {
                $shiporder = new Shiporder();
                $shiporder->orderId = $data['orderid'];
                $shiporder->personId = $data['orderperson'];
                $entityManager->persist($shiporder);
            }

            $shiptoData = $data['shipto'];
            $shipto = $this->getDoctrine()
                ->getRepository(Shipto::class)
                ->findOneBy(array('shiporder' => $shiporder));
            if (!empty($shipto)) {
                $entityManager->merge($shipto);
            } else {
                $shipto = new Shipto();
                $shipto->setName($data['name']);
                $shipto->setAddress($shiptoData['address']);
                $shipto->setCity($shiptoData['city']);
                $shipto->setCountry($shiptoData['country']);
                $shiporder->setShipto($shipto);
            }
            $item = $shipto = $this->getDoctrine()
                ->getRepository(Item::class)
                ->findOneBy(array('shiporder' => $shiporder));
            if (!empty($item)) {
                $itemData = $data['items']['item'];
                if (!array_key_exists(0, $itemData)) {//todo arrumar aqui
                    $itemData = array(0 => $itemData);
                }
                foreach ($itemData as $itemValue) {
                    $item = new Item();
                    $item->setTitle($itemValue['title']);
                    $item->setNote($itemValue['note']);
                    $item->setQuantity($itemValue['quantity']);
                    $item->setPrice($itemValue['price']);

                    $shiporder->getItems()->add($item);
                }
            }
        }//todo salvar e flush

    }
}
