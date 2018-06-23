<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Currency;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction()
    {
        try {
            $repository = $this->getDoctrine()->getRepository(Currency::class);
            // fetch data from database
            $currencies = $repository->findAll();
            // render the page and pass the data
            return $this->render(
                'default/index.html.twig',
                array(
                    'currencies' => $currencies,
                )
            );

        } catch (\Exception $e) {
            $e = $e->getMessage();

            return $this->json(array($e));
        }
    }
}