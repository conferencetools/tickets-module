<?php

namespace OpenTickets\Tickets\Controller;

use Carnage\Cqrs\MessageBus\MessageBusInterface;
use Doctrine\ORM\EntityManager;
use OpenTickets\Tickets\Domain\Command\Ticket\AssignToDelegate;
use OpenTickets\Tickets\Domain\Command\Ticket\CompletePurchase;
use OpenTickets\Tickets\Domain\Command\Ticket\ReserveTickets;
use OpenTickets\Tickets\Domain\Event\Ticket\TicketPurchaseCreated;
use OpenTickets\Tickets\Domain\ReadModel\TicketCounts\TicketCounter;
use OpenTickets\Tickets\Domain\ReadModel\TicketRecord\PurchaseRecord;
use OpenTickets\Tickets\Domain\Service\Configuration;
use OpenTickets\Tickets\Domain\Service\TicketAvailability\TicketAvailability;
use OpenTickets\Tickets\Domain\ValueObject\Delegate;
use OpenTickets\Tickets\Domain\ValueObject\TicketReservationRequest;
use OpenTickets\Tickets\Form\ManageTicket;
use OpenTickets\Tickets\Form\PurchaseForm;
use Zend\Form\FormElementManager\FormElementManagerV2Polyfill;
use Zend\Stdlib\ArrayObject;
use Zend\View\Model\ViewModel;
use ZfrStripe\Client\StripeClient;
use ZfrStripe\Exception\CardErrorException;

class TicketController extends AbstractController
{
    private static $cardErrorMessages = [
        'invalid_number' => 'The card number is not a valid credit card number.',
        'invalid_expiry_month' => 'The card\'s expiration month is invalid.',
        'invalid_expiry_year' => 'The card\'s expiration year is invalid.',
        'invalid_cvc' => 'The card\'s security code/CVC is invalid.',
        'invalid_swipe_data' => 'The card\'s swipe data is invalid.',
        'incorrect_number' => 'The card number is incorrect.',
        'expired_card' => 'The card has expired.',
        'incorrect_cvc' => 'The card\'s security code/CVC is incorrect.',
        'incorrect_zip' => 'The address for your card did not match the card\'s billing address.',
        'card_declined' => 'The card was declined.',
        'missing' => 'There is no card on a customer that is being charged.',
        'processing_error' => 'An error occurred while processing the card.',
    ];
    /**
     * @var TicketAvailability
     */
    private $ticketAvailability;
    /**
     * @var FormElementManagerV2Polyfill
     */
    private $formElementManager;

    public function __construct(
        MessageBusInterface $commandBus,
        EntityManager $entityManager,
        StripeClient $stripeClient,
        Configuration $configuration,
        TicketAvailability $ticketAvailability,
        FormElementManagerV2Polyfill $formElementManager
    ) {
        parent::__construct($commandBus, $entityManager, $stripeClient, $configuration);
        $this->ticketAvailability = $ticketAvailability;
        $this->formElementManager = $formElementManager;
    }

    public function indexAction()
    {
        return $this->redirect()->toRoute('root/select-tickets');
    }

    public function selectTicketsAction()
    {
        $tickets = $this->ticketAvailability->fetchAllAvailableTickets();

        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost();
            $failed = false;
            try {
                $purchases = $this->validateSelectedTickets($data, $tickets);
            } catch (\InvalidArgumentException $e) {
                $failed = true;
            }

            try {
                $discountCode = $this->validateDiscountCode($data);
                $discountCodeStr = $data['discount_code'];
            } catch (\InvalidArgumentException $e) {
                $failed = true;
                $discountCodeStr = '';
            }

            if (!$failed) {
                if ($discountCode !== null) {
                    $command = ReserveTickets::withDiscountCode($discountCode, ...$purchases);
                } else {
                    $command = new ReserveTickets(...$purchases);
                }

                $this->getCommandBus()->dispatch($command);
                /** @var TicketPurchaseCreated $event */
                $event = $this->events()->getEventsByType(TicketPurchaseCreated::class)[0];
                return $this->redirect()->toRoute('root/purchase', ['purchaseId' => $event->getId()]);
            }
        } else {
            try {
                $discountCodeStr = $this->params()->fromRoute('discount-code');
                $this->validateDiscountCode(['discount_code' => $discountCodeStr]);
            } catch (\InvalidArgumentException $e) {
                $discountCodeStr = '';
            }
        }

        return new ViewModel(['tickets' => $tickets, 'discountCode' => $discountCodeStr]);
    }

    /**
     * @param $data
     * @param TicketCounter[] $tickets
     * @return array
     */
    private function validateSelectedTickets($data, $tickets): array
    {
        $total = 0;
        $purchases = [];
        $errors = false;
        foreach ($data['quantity'] as $id => $quantity) {
            if (!is_numeric($quantity) || $quantity < 0) {
                $this->flashMessenger()->addErrorMessage('Quantity needs to be a number :)');
                $errors = true;
            } elseif (!$this->ticketAvailability->isAvailable($tickets[$id]->getTicketType(), $quantity)) {
                $this->flashMessenger()->addErrorMessage(
                    sprintf('Not enough %s remaining', $tickets[$id]->getTicketType()->getDisplayName())
                );
                $total++;
                $errors = true;
            } elseif ($quantity > 0) {
                $total += $quantity;
                $purchases[] = new TicketReservationRequest($tickets[$id]->getTicketType(), (int) $quantity);
            }
        }

        if ($total < 1) {
            $this->flashMessenger()->addErrorMessage('You must specify at least 1 ticket to purchase');
            $errors = true;
        }

        if ($errors) {
            throw new \InvalidArgumentException('input contained errors');
        }

        return $purchases;
    }

    /**
     * @param $data
     * @return ?DiscountCode
     */
    private function validateDiscountCode($data)
    {
        $errors = false;

        $validCodes = $this->getConfiguration()->getDiscountCodes();
        $validCodes[''] = null;

        $discountCode = strtolower($data['discount_code']);

        if (!array_key_exists($discountCode, $validCodes)) {
            $this->flashMessenger()->addErrorMessage('Invalid discount code');
            $errors = true;
        }

        if ($errors) {
            throw new \InvalidArgumentException('input contained errors');
        }

        return $validCodes[$discountCode];
    }

    public function purchaseAction()
    {
        $purchaseId = $this->params()->fromRoute('purchaseId');
        $noPayment = false;
        $purchase = $this->fetchPurchaseRecord($purchaseId);

        if ($purchase === null || $purchase->hasTimedout()) {
            $this->flashMessenger()->addErrorMessage('Purchase Id invalid or your purchase timed out');
            return $this->redirect()->toRoute('root/select-tickets');
        }

        if ($purchase->isPaid()) {
            $this->flashMessenger()->addInfoMessage('This purchase has already been paid for');
            return $this->redirect()->toRoute('root/complete', ['purchaseId' => $purchaseId]);
        }

        $form = new PurchaseForm($purchase->getTicketCount());

        if ($this->getRequest()->isPost()) {
            $noPayment = true;
            $data = $this->params()->fromPost();
            $form->setData($data);
            if ($form->isValid()) {
                try {
                    $this->getStripeClient()->createCharge([
                        "amount" => $purchase->getTotalCost()->getGross()->getAmount(),
                        "currency" => $purchase->getTotalCost()->getGross()->getCurrency(),
                        'source' => $data['stripe_token'],
                        'metadata' => [
                            'email' => $data['purchase_email'],
                            'purchaseId' => $purchaseId
                        ]
                    ]);

                    $delegateInfo = [];

                    for ($i = 0; $i < $purchase->getTicketCount(); $i++) {
                        $delegateInfo[] = Delegate::fromArray($data['delegates_' . $i]);
                    }

                    $command = new CompletePurchase($purchaseId, $data['purchase_email'], ...$delegateInfo);
                    $this->getCommandBus()->dispatch($command);
                    $this->flashMessenger()
                        ->addSuccessMessage(
                            'Your ticket purchase is completed. ' .
                            'You will receive an email shortly with your receipt. ' .
                            'Tickets will be sent to the delegates shortly before the event'
                        );
                    return $this->redirect()->toRoute('root/complete', ['purchaseId' => $purchaseId]);
                } catch (CardErrorException $e) {
                    $this->flashMessenger()->addErrorMessage(
                        sprintf(
                            'There was an issue with taking your payment: %s Please try again.',
                            $this->getDetailedErrorMessage($e)
                        )
                    );
                    $noPayment = false;
                }
            }
        }

        $this->flashMessenger()->addInfoMessage('Your tickets have been reserved for 30 mins, please complete payment before then');
        return new ViewModel(['purchase' => $purchase, 'form' => $form, 'noPayment' => $noPayment]);
    }

    /**
     * @param $purchaseId
     * @return PurchaseRecord|null
     */
    private function fetchPurchaseRecord($purchaseId)
    {
        /** @var PurchaseRecord $purchase */
        $purchase = $this->getEntityManager()->getRepository(PurchaseRecord::class)->findOneBy([
            'purchaseId' => $purchaseId
        ]);
        return $purchase;
    }

    private function getDetailedErrorMessage(CardErrorException $e)
    {
        $response = $e->getResponse();
        $errors = json_decode($response->getBody(true), true);
        $code = isset($errors['error']['code']) ? $errors['error']['code'] : 'processing_error';
        $code = isset(static::$cardErrorMessages[$code]) ? $code : 'processing_error';

        return static::$cardErrorMessages[$code];
    }

    public function completeAction()
    {
        $purchaseId = $this->params()->fromRoute('purchaseId');
        $purchase = $this->fetchPurchaseRecord($purchaseId);

        if ($purchase === null) {
            $this->flashMessenger()->addErrorMessage('Purchase Id invalid');
            return $this->redirect()->toRoute('root/select-tickets');
        }

        return new ViewModel(['purchase' => $purchase]);
    }

    public function manageAction()
    {
        $purchaseId = $this->params()->fromRoute('purchaseId');
        $ticketId = $this->params()->fromRoute('ticketId');

        $purchase = $this->fetchPurchaseRecord($purchaseId);
        $ticketRecord = $purchase->getTicketRecord($ticketId);
        $delegate = $ticketRecord->getDelegate();

        $form = $this->formElementManager->get(ManageTicket::class);
        $data = [
            'delegate' => [
                'firstname' => $delegate->getFirstname(),
                'lastname' => $delegate->getLastname(),
                'email' => $delegate->getEmail(),
                'company' => $delegate->getCompany(),
                'twitter' => $delegate->getTwitter(),
                'requirements' => $delegate->getRequirements()
            ]
        ];

        $form->bind(new ArrayObject($data));

        if ($this->getRequest()->isPost()) {
            $form->setData($this->params()->fromPost());
            if ($form->isValid()) {
                $data = $form->getData();
                $newDelegateInfo = Delegate::fromArray($data['delegate']);

                $command = new AssignToDelegate($newDelegateInfo, $ticketId, $purchaseId);
                $this->getCommandBus()->dispatch($command);
                $this->flashMessenger()
                    ->addSuccessMessage(
                        'Details updated successfully'
                    );
                return $this->redirect()->refresh();
            }
        }

        return new ViewModel(['purchase' => $purchase, 'form' => $form]);
    }
}
