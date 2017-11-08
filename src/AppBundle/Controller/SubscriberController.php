<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Subscriber;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Subscriber controller.
 *
 * @Route("admin/subscriber")
 */
class SubscriberController extends Controller
{
    /**
     * Lists all subscriber entities.
     *
     * @Route("/", name="subscriber_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $subscribers = $em->getRepository('AppBundle:Subscriber')->findAll();

        return $this->render('subscriber/index.html.twig', array(
            'subscribers' => $subscribers,
        ));
    }

    /**
     * Creates a new subscriber entity.
     *
     * @Route("/new", name="subscriber_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $subscriber = new Subscriber();
        $form = $this->createForm('AppBundle\Form\SubscriberType', $subscriber);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($subscriber);
            $em->flush();

            $this->get('subscriber_service')->fireEventForSubscriber($subscriber);

            return $this->redirectToRoute('subscriber_show', array('id' => $subscriber->getId()));
        }

        return $this->render('subscriber/new.html.twig', array(
            'subscriber' => $subscriber,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a subscriber entity.
     *
     * @Route("/{id}", name="subscriber_show")
     * @Method("GET")
     */
    public function showAction(Subscriber $subscriber)
    {
        $deleteForm = $this->createDeleteForm($subscriber);

        return $this->render('subscriber/show.html.twig', array(
            'subscriber' => $subscriber,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing subscriber entity.
     *
     * @Route("/{id}/edit", name="subscriber_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Subscriber $subscriber)
    {
        $deleteForm = $this->createDeleteForm($subscriber);
        $editForm = $this->createForm('AppBundle\Form\SubscriberType', $subscriber);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $this->get('subscriber_service')->fireEventForSubscriber($subscriber);

            return $this->redirectToRoute('subscriber_edit', array('id' => $subscriber->getId()));
        }

        return $this->render('subscriber/edit.html.twig', array(
            'subscriber' => $subscriber,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a subscriber entity.
     *
     * @Route("/{id}", name="subscriber_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Subscriber $subscriber)
    {
        $form = $this->createDeleteForm($subscriber);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $email = $subscriber->getEmail();
            $em = $this->getDoctrine()->getManager();
            $em->remove($subscriber);
            $em->flush();

            $this->get('subscriber_service')->fireUnsubscribeEvent($email);
        }

        return $this->redirectToRoute('subscriber_index');
    }

    /**
     * Creates a form to delete a subscriber entity.
     *
     * @param Subscriber $subscriber The subscriber entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Subscriber $subscriber)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('subscriber_delete', array('id' => $subscriber->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
