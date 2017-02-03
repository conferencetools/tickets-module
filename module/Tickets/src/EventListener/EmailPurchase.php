<?php

namespace OpenTickets\Tickets\EventListener;

use Carnage\Cqrs\Event\DomainMessage;
use Carnage\Cqrs\MessageBus\MessageInterface;
use Carnage\Cqrs\MessageHandler\MessageHandlerInterface;
use Crossjoin\PreMailer\HtmlString;
use Doctrine\ORM\EntityManagerInterface;
use OpenTickets\Tickets\Domain\Event\Ticket\TicketPurchasePaid;
use OpenTickets\Tickets\Domain\ReadModel\TicketRecord\PurchaseRecord;
use Zend\Http\Response;
use Zend\Mail\Message;
use Zend\Mail\Transport\TransportInterface;
use Zend\Mime\Part as MimePart;
use Zend\Mime\Message as MimeMessage;
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
     * EmailPurchase constructor.
     * @param $em
     */
    public function __construct(EntityManagerInterface $em, View $view, TransportInterface $mail)
    {
        $this->em = $em;
        $this->view = $view;
        $this->mail = $mail;
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
            'purchaseId' => $message->getId()
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

        /*   $text = new MimePart($preMailer->getText());
        $text->type = "text/plain";
*/
        $html = new MimePart($htmlMarkup);
        $html->type = "text/html";

        $body = new MimeMessage();
        $body->setParts(array($html));

        $message = new Message();
        $message->setBody($body);
        $message->setSubject('Your PHP Yorkshire Ticket Receipt');
        $message->setFrom('info@phpyorkshire.co.uk');

        return $message;
    }
}