<?php
namespace OC\PlatformBundle\Antispam;


class OCAntispamMailer
{
    /**
     * @var \Swift_Mailer
     */
    private $mailer;

    /**
     * @var string
     */
    private $spamMailTarget;
    /**
     * @var string
     */
    private $spamMailSender;

    /**
     * OCAntispamMailer constructor.
     * @param \Swift_Mailer $mailer
     * @param string $spamMailTarget
     * @param string $spamMailSender
     */
    public function __construct(\Swift_Mailer $mailer, $spamMailTarget, $spamMailSender)
    {
        $this->mailer = $mailer;
        $this->spamMailTarget = $spamMailTarget;
        $this->spamMailSender = $spamMailSender;
    }


    public function sendMailForSpamDetected($text){
        /**
         * @var \Swift_Message $message
         */
        $message = $this->mailer->createMessage();
        $message->addTo($this->spamMailTarget)
                ->setBody('Ce texte a été considéré comme du spam : '.$text)
                ->setFrom($this->spamMailSender)
        ;
        if (!$this->mailer->send($message)){
            throw new \Exception('mail not sent');   
        }
        
    }


}