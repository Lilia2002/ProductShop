<?php


namespace App\Service;


use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;


class EmailService implements EmailServiceInterface
{
    /** @var MailerInterface  */
    private $mailer;

    public function __construct( MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function sendOrderStatusChangedEmail(string $to, string $status)
    {
        $email = new Email();

        $email
            ->from('devzimalab@gmail.com')
            ->to($to)
            ->subject('Cтатус Вашего заказа был изменен')
            ->html('<p>Статус Вашего заказа был изменен на '.$status.'</p>')
        ;

        $this->mailer->send($email);
    }

    public function exportCompletedEmail(string $to)
    {
        $email = (new Email())
            ->from('devzimalab@gmail.com')
            ->to($to)
            ->subject('Экспорт данных')
            ->html('<p>Данные успешно добавлены в базу!</p>')
        ;

        $this->mailer->send($email);
    }
}