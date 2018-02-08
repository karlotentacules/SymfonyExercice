<?php

namespace OC\PlatformBundle\Antispam;


class OCAntispam
{
    /**
     * @var integer
     */
    private $limitCharsNumber;

    /**
     * @var OCAntispamMailer
     */
    private $spamMailer;

    /**
     * OCAntispam constructor.
     * @param int $limitCharsNumber
     * @param OCAntispamMailer $spamMailer
     */
    public function __construct($limitCharsNumber, OCAntispamMailer $spamMailer)
    {
        $this->limitCharsNumber = $limitCharsNumber;
        $this->spamMailer = $spamMailer;
    }



    public function isSpam($text,$autoSendMail = false){
        $isSpam = strlen($text)>$this->limitCharsNumber;
        if ($isSpam && $autoSendMail){
            $this->spamMailer->sendMailForSpamDetected($text);
        }
        
        return $isSpam;
    }

}