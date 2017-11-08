<?php

namespace AppBundle\Service;

use AppBundle\Entity\Subscriber;
use AppBundle\Repository\SubscriberRepository;
use Welp\MailchimpBundle\Provider\ProviderInterface;
use Welp\MailchimpBundle\Subscriber\Subscriber as MCSubscriber;

class SubscriberProvider implements ProviderInterface
{
    /**
     * @var SubscriberRepository
     */
    protected $repo;

    public function __construct(SubscriberRepository $repo)
    {
        $this->repo = $repo;
    }

    /**
     * @return MCSubscriber[]
     */
    public function getSubscribers()
    {
        $users = $this->repo->findAll();

        $subscribers = array_map(function(Subscriber $user) {
            $subscriber = new MCSubscriber($user->getEmail(), [], [
                'language'   => 'en',
                'email_type' => 'html'
            ]);

            return $subscriber;
        }, $users);

        return $subscribers;
    }

}
