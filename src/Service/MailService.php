<?php

namespace src\Service;

use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mime\Email;

class MailService
{
    private Mailer $mailer;
    public function __construct()
    {
        $transport = Transport::fromDsn('smtp://3dd84281bc8679:8a9180301c670a@sandbox.smtp.mailtrap.io:2525');
        $this->mailer = new Mailer($transport);
    }

    public function send(array|string $from,array|string $to, string $sujet, string $html )
    {
        $email = (new Email())
            ->from($from)
            ->to($to)
            ->subject($sujet)
            ->html($html);
        $this->mailer->send($email);
    }

}