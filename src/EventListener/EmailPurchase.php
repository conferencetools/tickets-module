<?php

/*
 * This file is part of PHP CS Fixer.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace ConferenceTools\Tickets\EventListener;

use Carnage\Cqrs\Event\DomainMessage;
use Carnage\Cqrs\MessageBus\MessageInterface;
use Carnage\Cqrs\MessageHandler\MessageHandlerInterface;
use ConferenceTools\Tickets\Domain\Event\Ticket\TicketPurchasePaid;
use ConferenceTools\Tickets\Domain\ReadModel\TicketRecord\PurchaseRecord;
use Doctrine\ORM\EntityManagerInterface;
use Zend\Http\Response;
use Zend\Mail\Message;
use Zend\Mail\Transport\TransportInterface;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Part as MimePart;
use Zend\View\Model\ViewModel;
use Zend\View\View;

class EmailPurchase implements MessageHandlerInterface
{
    private $em;
    /**
     * @var View
     */
    private $view;
    /**
     * @var TransportInterface
     */
    private $mail;
    /**
     * @var array
     */
    private $config;

    /**
     * EmailPurchase constructor.
     *
     * @param EntityManagerInterface $em
     * @param View                   $view
     * @param TransportInterface     $mail
     * @param array                  $config
     */
    public function __construct(EntityManagerInterface $em, View $view, TransportInterface $mail, array $config = [])
    {
        $this->em = $em;
        $this->view = $view;
        $this->mail = $mail;
        $this->config = $config;
    }

    public function handleDomainMessage(DomainMessage $message)
    {
        $this->handle($message->getEvent());
    }

    public function handle(MessageInterface $message)
    {
        if (!($message instanceof TicketPurchasePaid)) {
            return;
        }

        $purchase = $this->em->getRepository(PurchaseRecord::class)->findOneBy([
            'purchaseId' => $message->getId(),
        ]);

        $viewModel = new ViewModel(['purchase' => $purchase]);
        $viewModel->setTemplate('email/receipt');

        $response = new Response();
        $this->view->setResponse($response);
        $this->view->render($viewModel);
        $html = $response->getContent();

        $emailMessage = $this->buildMessage($html);
        $emailMessage->setTo($message->getPurchaserEmail());

        $this->mail->send($emailMessage);
    }

    private function buildMessage($htmlMarkup)
    {
        $html = new MimePart($htmlMarkup);
        $html->setCharset('UTF-8');
        $html->type = 'text/html';

        $body = new MimeMessage();
        $body->setParts([$html]);

        $message = new Message();
        $message->setBody($body);
        $message->setSubject($this->config['subject'] ?? 'Your ticket receipt');
        if (isset($this->config['from'])) {
            $message->setFrom($this->config['from']);
        }
        $message->setEncoding('UTF-8');

        return $message;
    }
}
