<?php

namespace OC\PlatformBundle\Listener;


use Doctrine\ORM\Event\LifecycleEventArgs;
use OC\PlatformBundle\Entity\Application;

class ApplicationDoctrineListener
{
    /**
     * @var \Swift_Mailer
     */
    private $mailer;

    
    private $spamMailTarget;
    
    private $spamMailSender;

    /**
     * ApplicationDoctrineListener constructor.
     * @param \Swift_Mailer $mailer
     * @param $spamMailTarget
     * @param $spamMailSender
     */
    public function __construct(\Swift_Mailer $mailer, $spamMailTarget, $spamMailSender)
    {
        $this->mailer = $mailer;
        $this->spamMailTarget = $spamMailTarget;
        $this->spamMailSender = $spamMailSender;
    }


    public function postPersist(LifecycleEventArgs $event){
        $entity = $event->getEntity();
        if ($entity instanceof Application){
            
            /**
             * @var \Swift_Message $message
             */
            $message = $this->mailer->createMessage();
            $message->addTo($this->spamMailTarget)
                ->setBody('Une nouvelle applciation a été créée ici')
                ->setFrom($this->spamMailSender)
                ->setSubject('Applications auto service')
            ;
            if (!$this->mailer->send($message)){
                throw new \Exception('mail not sent');
            }
        }
    }

}