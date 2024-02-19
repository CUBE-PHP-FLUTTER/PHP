<?php

namespace src\Controller;

use src\Service\MailService;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mime\Email;

class ContactController extends AbstractController
{
    public function form()
    {
        return $this->twig->render("Contact/form.html.twig");
    }

    public function send()
    {
        $mail = new MailService();
        $mail->send(
            from: $_POST["mail"],
            to: "admin@cesiblog.fr",
            sujet: "Contact depuisle formulaire",
            html: $this->twig->render("Mailing/contact.html.twig",[
                "nom"=>$_POST["nom"],
                "message"=>$_POST["message"],
            ])
        );
        header("Location:/Contact/form");
    }
}