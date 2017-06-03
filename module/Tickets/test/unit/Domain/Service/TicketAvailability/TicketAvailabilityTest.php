<?php

namespace ConferenceTools\Tickets\Domain\Service\TicketAvailability;

use Doctrine\Common\Collections\ArrayCollection;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use ConferenceTools\Tickets\Domain\Finder\TicketCounterInterface;
use ConferenceTools\Tickets\Domain\ReadModel\TicketCounts\TicketCounter;
use ConferenceTools\Tickets\Domain\Service\Configuration;
use Mockery as m;

class TicketAvailabilityTest extends MockeryTestCase
{
    /**
     * @var Configuration
     */
    private $config;

    /**
     * @var ArrayCollection
     */
    private $ticketCounters;

    public function __construct($name = null, array $data = array(), $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->config = Configuration::fromArray([
            'tickets' => [
                'early' => ['name' => 'Early Bird', 'cost' => 5000, 'available' => 75],
                'std' => ['name' => 'Standard', 'cost' => 10000, 'available' => 150],
                'free' => ['name' => 'Free', 'cost' => 0, 'available' => 0]
            ],
            'financial' => [
                'taxRate' => 10,
                'currency' => 'GBP',
                'displayTax' => true
            ]
        ]);
        $ticketCounters['early'] = new TicketCounter(
            $this->config->getTicketType('early'),
            $this->config->getAvailableTickets('early')
        );

        $ticketCounters['std'] = new TicketCounter(
            $this->config->getTicketType('std'),
            $this->config->getAvailableTickets('std')
        );

        $ticketCounters['free'] = new TicketCounter(
            $this->config->getTicketType('free'),
            $this->config->getAvailableTickets('free')
        );

        $this->ticketCounters= new ArrayCollection($ticketCounters);
    }

    public function testFetchAllAvailableTickets()
    {
        $mockFinder = m::mock(TicketCounterInterface::class);
        $mockFinder->shouldReceive('byTicketTypeIdentifiers')
            ->with('early', 'std', 'free')
            ->andReturn($this->ticketCounters);

        $sut = new TicketAvailability(new TicketTypeFilter($this->config), $mockFinder);

        $result = $sut->fetchAllAvailableTickets();

        self::assertTrue($result->count() === 2, 'The expected number of tickets was not returned');
        self::assertFalse($result->contains($this->ticketCounters['free']), 'Free tickets were included in the result');
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
        $mockFinder = m::mock(TicketCounterInterface::class);
        $mockFinder->shouldReceive('byTicketTypeIdentifiers')
            ->with('early', 'std', 'free')
            ->andReturn($this->ticketCounters);

        $sut = new TicketAvailability(new TicketTypeFilter($this->config), $mockFinder);

        self::assertEquals($expected, $sut->isAvailable($ticketType, $quantity));
    }

    public function provideIsAvailable()
    {
        return [
            [$this->config->getTicketType('free'), 1, false],
            [$this->config->getTicketType('std'), 1, true],
            [$this->config->getTicketType('early'), 76, false],
        ];
    }
}
