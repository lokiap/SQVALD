<?php

namespace App\Services;
use App\Entity\EmailModel;
use App\Entity\User;
use Mailjet\Client;
use Mailjet\Resources;

class EmailSender
{
    public function sendEmail(User $users, EmailModel $email)
    {
        $mj = new Client(getenv('MJ_APIKEY_PUBLIC'), getenv('MJ_APIKEY_PRIVATE'),true,['version' => 'v3.1']);
         $body = [
            'Messages' => [
                [
                    'From' => [
                        'Email' => "mawaraba@gmail.com",
                        'Name' => "ProjetWeb contact"
                    ],
                    'To' => [
                        [
                            'Email' => $users->getEmail(),
                            'Name' => $users->getUsername()
                        ]
                    ],
                    'TemplateID' => 2991103,
                    'TemplateLanguage' => true,
                    'Subject' => $email->getSubject(),
                    'Variables' => [
                        'title' => $email->getTitle(),
                        'content' => $email->getContent()

                    ]
                ]
            ]
        ];
        $response = $mj->post(Resources::$Email, ['body' => $body]);
        $response->success() && dd($response->getData());
    }
}
