<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Subscriber;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/project-info", name="homepage")
     */
    public function indexAction(Request $request)
    {
        // replace this example code with whatever you need
        return $this->render('default/index.html.twig', array(
            'base_dir' => realpath($this->container->getParameter('kernel.root_dir').'/..').DIRECTORY_SEPARATOR,
        ));
    }

    /**
     * @Route("/", name="subscribe")
     */
    public function subscribeAction(Request $request)
    {
        $email = $request->get('email');
        $errorMessage = '';

        if (!empty($email)) {

            $subscriberService = $this->container->get('subscriber_service');

            try {
                $subscriber = new Subscriber();
                $subscriber->setEmail($email);
                $validator = $this->get('validator');
                $errors = $validator->validate($subscriber);
                if (count($errors)) {
                    $first = $errors->get(0);
                    $errorMessage = $first->getMessage();
                    throw new Exception($errorMessage);
                }

                $subscriberService->addSubscriber($email);

                return $this->render('@App/Default/subscribed.html.twig');
            } catch (UniqueConstraintViolationException $e) {
                $errorMessage = $email . ' is already in subscribers list';
            } catch (\Exception $e) {
                $errorMessage = $e->getMessage();
            }

        }

        return $this->render('@App/Default/subscribe.html.twig', [
            'errorMessage' => $errorMessage,
        ]);
    }
}
