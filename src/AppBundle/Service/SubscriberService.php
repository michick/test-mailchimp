<?php

namespace AppBundle\Service;

use AppBundle\Entity\Subscriber;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpKernel\Debug\TraceableEventDispatcher;
use Welp\MailchimpBundle\Event\SubscriberEvent;
use Welp\MailchimpBundle\Subscriber\Subscriber as MCSubscriber;

class SubscriberService
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var TraceableEventDispatcher
     */
    protected $dispatcher;

    /**
     * SubscriberService constructor.
     * @param EntityManager $em
     * @param TraceableEventDispatcher $dispatcher
     */
    public function __construct(EntityManager $em, TraceableEventDispatcher $dispatcher)
    {
        $this->em = $em;
        $this->dispatcher = $dispatcher;
    }

    /**
     * @param $email
     * @param bool $optIn
     * @return Subscriber
     */
    public function addSubscriber($email, $optIn = true)
    {
        $subscriber = new Subscriber();

        $subscriber->setEmail($email);
        $subscriber->setOptin($optIn);

        $this->em->persist($subscriber);
        $this->em->flush();

        $this->fireSubscribeEvent($subscriber->getEmail());

        return $subscriber;
    }

    /**
     * @param string $email
     */
    public function fireSubscribeEvent($email)
    {
        $this->fireEvent($email);
    }

    /**
     * @param string $email
     */
    public function fireUnsubscribeEvent($email)
    {
        $this->fireEvent($email, false);
    }

    /**
     * @param Subscriber $subscriber
     */
    public function fireEventForSubscriber(Subscriber $subscriber)
    {
        $this->fireEvent($subscriber->getEmail(), $subscriber->getOptin());
    }

    /**
     * @param string $email
     * @param bool $optin
     */
    protected function fireEvent($email, $optin = true)
    {
        $event = $optin ? SubscriberEvent::EVENT_SUBSCRIBE : SubscriberEvent::EVENT_UNSUBSCRIBE;
        $mcSubscriber = new MCSubscriber($email);

        $this->dispatcher->dispatch(
            $event,
            new SubscriberEvent('241034b726', $mcSubscriber)
        );
    }
}
