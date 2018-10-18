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

namespace ConferenceTools\Tickets\Domain\Service\Availability;

use Carnage\Cqrs\Persistence\ReadModel\RepositoryInterface;
use ConferenceTools\Tickets\Domain\ReadModel\TicketCounts\TicketCounter;
use ConferenceTools\Tickets\Domain\Service\Availability\Filters\FilterInterface;
use ConferenceTools\Tickets\Domain\Service\Configuration;
use Doctrine\Common\Collections\ArrayCollection;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;

/**
 * @internal
 * @coversNothing
 */
final class TicketAvailabilityTest extends MockeryTestCase
{
    /**
     * @var Configuration
     */
    private $config;

    /**
     * @var ArrayCollection
     */
    private $ticketCounters;

    /**
     * @var FilterInterface[]
     */
    private $filters;

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $settings = [
            'tickets' => [
                'early' => ['name' => 'Early Bird', 'cost' => 5000, 'available' => 75],
                'std' => ['name' => 'Standard', 'cost' => 10000, 'available' => 150],
                'free' => ['name' => 'Free', 'cost' => 0, 'available' => 100, 'metadata' => ['private' => true]],
                'expired' => ['name' => 'Expired', 'cost' => 1000, 'available' => 100, 'metadata' => [
                    'availableFrom' => (new \DateTime())->sub(new \DateInterval('P1D')),
                    'availableTo' => (new \DateTime())->sub(new \DateInterval('P1D')),
                ]],
                'after_early' => ['name' => 'After Early', 'cost' => 7500, 'available' => 100, 'metadata' => [
                    'after' => ['early'],
                ]],
                'after_expired' => ['name' => 'After Expired', 'cost' => 2500, 'available' => 100, 'metadata' => [
                    'after' => ['expired'],
                ]],
                'soldout' => ['name' => 'Sold out', 'cost' => 1500, 'available' => 0, 'metadata' => [
                ]],
                'after_soldout' => ['name' => 'After Sold out', 'cost' => 3500, 'available' => 100, 'metadata' => [
                    'after' => ['soldout'],
                ]],
            ],
            'financial' => [
                'taxRate' => 10,
                'currency' => 'GBP',
                'displayTax' => true,
            ],
        ];
        $this->config = Configuration::fromArray($settings);

        $ticketCounters = [];
        foreach (array_keys($settings['tickets']) as $identifier) {
            $ticketCounters[$identifier] = new TicketCounter(
                $this->config->getTicketType($identifier),
                $this->config->getAvailableTickets($identifier)
            );
        }

        $this->ticketCounters = new ArrayCollection($ticketCounters);

        $this->filters = [
            new Filters\IsAvailable(),
            new Filters\AfterSoldOut($this->config),
            new Filters\ByDate($this->config),
            new Filters\IsPrivate($this->config),
        ];
    }

    public function testFetchAllAvailableTickets()
    {
        $mockFinder = m::mock(RepositoryInterface::class);
        $mockFinder->shouldReceive('matching')->andReturn($this->ticketCounters);

        $sut = new TicketAvailability($mockFinder, ...$this->filters);

        $result = $sut->fetchAllAvailableTickets();

        $this->assertTrue(4 === $result->count(), 'The expected number of tickets was not returned');
        $this->assertFalse($result->contains($this->ticketCounters['free']), 'Free tickets were included in the result');
    }

    /**
     * @dataProvider provideIsAvailable
     *
     * @param $ticketType
     * @param $quantity
     * @param $expected
     */
    public function testIsAvailable($ticketType, $quantity, $expected)
    {
        $mockFinder = m::mock(RepositoryInterface::class);
        $mockFinder->shouldReceive('matching')->andReturn($this->ticketCounters);

        $sut = new TicketAvailability($mockFinder, ...$this->filters);

        $this->assertSame($expected, $sut->isAvailable($ticketType, $quantity));
    }

    public function provideIsAvailable()
    {
        return [
            [$this->config->getTicketType('free'), 1, false],
            [$this->config->getTicketType('soldout'), 1, false],
            [$this->config->getTicketType('expired'), 1, false],
            [$this->config->getTicketType('after_early'), 1, false],
            [$this->config->getTicketType('early'), 76, false],
            [$this->config->getTicketType('after_expired'), 1, true],
            [$this->config->getTicketType('after_soldout'), 1, true],
            [$this->config->getTicketType('std'), 1, true],
            [$this->config->getTicketType('early'), 1, true],
        ];
    }
}
