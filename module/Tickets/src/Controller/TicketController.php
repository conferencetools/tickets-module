<?php

namespace OpenTickets\Tickets\Controller;

use OpenTickets\Tickets\Domain\Command\Ticket\CompletePurchase;
use OpenTickets\Tickets\Domain\Command\Ticket\ReserveTickets;
use OpenTickets\Tickets\Domain\Command\Ticket\TimeoutPurchase;
use OpenTickets\Tickets\Domain\Event\Ticket\TicketPurchaseCreated;
use OpenTickets\Tickets\Domain\ReadModel\TicketCounts\TicketCounter;
use OpenTickets\Tickets\Domain\ReadModel\TicketRecord\TicketRecord;
use OpenTickets\Tickets\Domain\ValueObject\Delegate;
use OpenTickets\Tickets\Domain\ValueObject\Money;
use OpenTickets\Tickets\Domain\ValueObject\TicketReservationRequest;
use OpenTickets\Tickets\Domain\ValueObject\TicketType;
use OpenTickets\Tickets\Form\PurchaseForm;
use Zend\View\Model\ViewModel;
use ZfrStripe\Client\StripeClient;
use ZfrStripe\Exception\CardErrorException;

class TicketController extends AbstractController
{
    public function setupAction()
    {
        $em = $this->getEntityManager();
        $x = new TicketCounter(new TicketType('sup_early', new Money(70, 'GBP'), 'Super Early Bird'), 25);
        $y = new TicketCounter(new TicketType('early', new Money(85, 'GBP'), 'Early Bird'), 75);
        $z = new TicketCounter(new TicketType('std', new Money(100, 'GBP'), 'Standard'), 150);

        $em->persist($x);
        $em->persist($y);
        $em->persist($z);
        $em->flush();
    }

    public function timeoutAction()
    {
        $qb = $this->getEntityManager()->getRepository(TicketRecord::class)->createQueryBuilder('tr');
        /** @var TicketRecord[] $timedout */
        $timedout = $qb->where('tr.createdAt < :time')
            ->andWhere('tr.delegate.email = \'\'')
            ->groupBy('tr.purchaseId')
            ->setParameter('time', new \DateTime('-30 minutes'))
            ->getQuery()
            ->getResult();

        foreach ($timedout as $ticketRecord) {
            $command = new TimeoutPurchase($ticketRecord->getPurchaseId());
            $this->getCommandBus()->dispatch($command);
        }
    }

    public function selectTicketsAction()
    {
        $qb = $this->getEntityManager()->getRepository(TicketCounter::class)->createQueryBuilder('t', 't.id');
        /** @var TicketCounter[] $tickets */
        $tickets = $qb->where($qb->expr()->gt('t.remaining', 0))->getQuery()->getResult();

        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost();
            $total = 0;
            $purchases = [];
            $errors = [];
            foreach($data['quantity'] as $id => $quantity) {
                if ($quantity > $tickets[$id]->getRemaining()) {
                    $errors[] = sprintf('Not enough %s remaining', $tickets[$id]->getTicketType()->getDisplayName());
                    $total++;
                } elseif (!is_numeric($quantity) || $quantity < 0) {
                    $errors[] = 'Quantity needs to be a number :)';
                } elseif ($quantity > 0) {
                    $total += $quantity;
                    $purchases[] = new TicketReservationRequest($tickets[$id]->getTicketType(), (int) $quantity);
                }
            }

            if ($total < 1) {
                $errors[] = 'You must specify at least 1 ticket to purchase';
            }

            if ($data['discount_code'] !== '') {
                $errors[] = 'Invalid discount code';
            }

            if (empty($errors)) {
                $command = new ReserveTickets(...$purchases);
                $this->getCommandBus()->dispatch($command);
                /** @var TicketPurchaseCreated $event */
                $event = $this->events()->getEventsByType(TicketPurchaseCreated::class)[0];
                $this->redirect()->toRoute('root/purchase', ['purchaseId' => $event->getId()]);
            }

            foreach ($errors as $error) {
                $this->flashMessenger()->addErrorMessage($error);
            }
        }

        return new ViewModel(['tickets' => $tickets]);
    }

    public function purchaseAction()
    {
        $purchaseId = $this->params()->fromRoute('purchaseId');
        $noPayment = false;
        //@TODO need a view model for calculated puchase information from domain model.
        $qb = $this->getEntityManager()->getRepository(TicketRecord::class)->createQueryBuilder('tr');

        $result = $qb->where('tr.purchaseId = :purchaseId')
            ->setParameter('purchaseId', $purchaseId)
            ->getQuery()
            ->getResult();

        $tickets = [];
        $totalTickets = 0;
        foreach ($result as $ticket) {
            /** @var TicketRecord $ticket */
            $tickets[$ticket->getTicketType()->getIdentifier()]['ticketType'] = $ticket->getTicketType();
            if (!isset($tickets[$ticket->getTicketType()->getIdentifier()]['quantity'])) {
                $tickets[$ticket->getTicketType()->getIdentifier()]['quantity'] = 0;
            }
            $tickets[$ticket->getTicketType()->getIdentifier()]['quantity']++;
            $totalTickets += 1;
        }

        $form = new PurchaseForm();

        if ($this->getRequest()->isPost()) {
            $noPayment = true;
            $data = $this->params()->fromPost();
            $form->setData($data);
            if ($form->isValid()) {
                /** @var StripeClient $stripeClient */
                $stripeClient = $this->getServiceLocator()->get(StripeClient::class);
                try {
                    $stripeClient->createCharge([
                        "amount" => 7000,
                        "currency" => 'gbp',
                        'source' => $data['stripe_token'],
                        'email' => $data['purchase_email']
                    ]);

                    $delegateInfo = [];

                    for ($i = 0; $i < $totalTickets; $i++) {
                        $delegateInfo[] = Delegate::fromArray($data['delegates_' . $i]);
                    }

                    $command = new CompletePurchase($purchaseId, $data['purchase_email'], ...$delegateInfo);
                    $this->getCommandBus()->dispatch($command);

                } catch (CardErrorException $e) {
                    $this->flashMessenger()->addErrorMessage('There was an issue with taking your payment, please try again');
                    $noPayment = false;
                }
            }
        }

        return new ViewModel(['tickets' => $tickets, 'form' => $form, 'noPayment' => $noPayment]);
    }
}