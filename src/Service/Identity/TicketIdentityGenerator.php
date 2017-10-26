<?php

namespace ConferenceTools\Tickets\Service\Identity;

use Carnage\Cqrs\Aggregate\Identity\GeneratorInterface;
use RandomLib\Factory;

/**
 * Class TicketIdentityGenerator
 *
 * Generates a short (7 character identifier)
 *
 * 10^-6 prob of collision for approx 1500 tickets, should be adequate for most events.
 * Purchase Id is also used when modifying tickets online so collisions are only really an issue for manual check ins
 * this problem can be resolved by using a sufficiently smart human.
 */
class TicketIdentityGenerator implements GeneratorInterface
{
    private $random;

    public function __construct()
    {
        $this->random = (new Factory())->getMediumStrengthGenerator();
    }

    public function generateIdentity()
    {
        return rtrim(strtr(base64_encode($this->random->generate(5)), '+/', '-_'), '=');
    }
}