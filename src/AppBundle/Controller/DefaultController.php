<?php

namespace AppBundle\Controller;

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
                $this->persistPerson($xml['person']);
//            } else {
//                if (array_key_exists('shiporder', $xml)) {
//                    $this->persistShiporder($xml['shiporder']);
//                }
            }
        }
        return $this->render('default/upload.html.twig', array(
            'number' => '10',
        ));
    }

    private function persistPerson($xml)
    {
        foreach ($xml as $personData) {
            $date = new \DateTime(gmdate('Y-m-d H:i:s'));
            $person = new Person();
//            $person->setP $personData->personid);
            $person->setPersonName($personData['personname']);
            $person->setPersonId($personData['personid']);
//            $person->setCreated($date);
//            $person->setUpdated($date);
            $entityManager = $this->getDoctrine()->getManager();
            $personExists = $this->getDoctrine()
                ->getRepository(Person::class)
                ->find($personData['personid']);
            if ($personExists) {
//                $person->setCreated($personExists->getCreated());
                //todo  adicionarcollect
//                $em->persist($user);
//                $user->setCollecImages($collecImages);
//                $em->flush();
                $entityManager->merge($person);
            } else {
                $entityManager->persist($person);
            }
            if (isset($personData['phones']) && isset($item->phones->phone) && count($personData['phones']['phone']) > 0) {
                foreach ($personData['phones']['phone'] AS $key => $phone) {
                    $phone = (array)$phone;
                    $phone = $phone[0];
                    $person_phone = new Phone();
                    $person_phone->setPerson($person);
                    $person_phone->setPhone($phone);

                    $entityManager = $this->getDoctrine()->getManager();
                    $phoneExists = $this->getDoctrine()
                        ->getRepository(Person::class)
                        ->findOneBy(array('phone' => $phone));
                    if ($phoneExists) {
//                        $person_phone->setId(intval($phoneExists->getId()));
//                        $person_phone->setCreated($phoneExists->getCreated());
                        $entityManager->merge($person_phone);
                    } else {
                        $entityManager->persist($person_phone);
                    }
                }
            }
            $entityManager->flush();
        }
    }

    private function persistShiporder($xml)
    {
        foreach ($xml as $shiporderData) {
            $shiporder = Shiporder::firstOrNew(array('orderId' => $shiporderData['orderid']));
            $shiporder->orderId = $shiporderData['orderid'];
            $shiporder->personId = $shiporderData['orderperson'];
            $shiporder->save();
            $shiptoData = $shiporderData['shipto'];
            $shipto = Shipto::firstOrNew(array('orderId' => $shiporderData['orderid']));
            $shipto->orderId = $shiporderData['orderid'];
            $shipto->name = $shiptoData['name'];
            $shipto->address = $shiptoData['address'];
            $shipto->city = $shiptoData['city'];
            $shipto->country = $shiptoData['country'];
            $shipto->save();
            Item::where('orderId', $shiporderData['orderid'])->delete();
            $itemData = $shiporderData['items']['item'];
            if (!array_key_exists(0, $itemData)) {
                $itemData = array(0 => $itemData);
            }
            foreach ($itemData as $itemValue) {
                $item = new Item();
                $item->orderId = $shiporderData['orderid'];
                $item->title = $itemValue['title'];
                $item->note = $itemValue['note'];
                $item->quantity = $itemValue['quantity'];
                $item->price = $itemValue['price'];
                $item->save();
            }
        }
    }

}
