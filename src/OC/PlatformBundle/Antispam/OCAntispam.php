<?php

namespace OC\PlatformBundle\Antispam;
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of OCAntispam
 *
 * @author alici
 */
class OCAntispam {

    private $mailer;
    private $locale;
    private $minLength;
    
    function __construct(\Swift_Mailer $mailer, $locale, $minLength) {
        $this->mailer = $mailer;
        $this->locale = $locale;
        $this->minLength = (int)$minLength;
    }

    /**
     * Vérifie si le texte est un spam ou non
     *
     * @param string $text
     * @return bool
     */
    public function isSpam($text) {
        return strlen($text) < $this->minLength;
    }

}
