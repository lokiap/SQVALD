<?php
// src/IPS/ConnexionBundle/Services/IPSFiltre.php

/*
 * This file is part of the Dirigami project.
 *
 * (c) Ben idrissa SYLLA <benidrissas@dirips.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Services;

use Twig\Environment;

/**
 * Cette classe ou service permet l'envoi de mail
 *
 * @author Ben idrissa SYLLA <benidrissas@dirips.com>
 */
class Mailer {

	private $mailer;

    private $twig;

    /**
     * Ce constructeur permet d'initialiser le service
     *
     * @param \Swift_Mailer $mailer librairie peremettant l'envoi de mail 
     * @param Environment $twig template utilisée pour l'affichage de données
     *
     * @return 
     */
    public function __construct(\Swift_Mailer $mailer, Environment $twig)
    {
        $this->mailer = $mailer;

        $this->twig = $twig;
    }

    /**     
     * Cette fonction permet l'envoi de mail avec un document joint
     *
     * @return
     */
    public function envoiMail() {
    	$body = $this->renderTemplate();

        $message = (new \Swift_Message('Test envoi invitation calendrier'))
            //->setSubject('Test envoi invitation calendrier')
            ->setFrom('mawaraba@gmail.com')
            ->setTo('benidrissas@dirips.com')
            ->setBody($body, 'text/html')
        ;
        var_dump("Debut envoi de mail");
        $this->mailer->send($message);
        var_dump("Fin envoi de mail");
    }

    /**     
     * Cette fonction retourne la vue mailer.html.twig contenue dans le répertoire Resources/Agenda
     *
     * @param 
     *
     * @return
     */
    public function renderTemplate() {
        return $this->twig->render(
            'mailer/mailer.html.twig', array(
        ));
	}
}