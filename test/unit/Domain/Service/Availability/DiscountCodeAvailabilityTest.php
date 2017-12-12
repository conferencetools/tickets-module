<?php

namespace ConferenceTools\Tickets\Domain\Service\Availability;

use Carnage\Cqrs\Persistence\ReadModel\RepositoryInterface;
use ConferenceTools\Tickets\Domain\Service\Availability\Filters\FilterInterface;
use ConferenceTools\Tickets\Domain\ValueObject\DiscountType;
use Doctrine\Common\Collections\ArrayCollection;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use ConferenceTools\Tickets\Domain\Finder\TicketCounterInterface;
use ConferenceTools\Tickets\Domain\ReadModel\TicketCounts\TicketCounter;
use ConferenceTools\Tickets\Domain\Service\Configuration;
use Mockery as m;

class DiscountCodeAvailabilityTest extends MockeryTestCase
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

    public function __construct($name = null, array $data = array(), $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->config = Configuration::fromArray([
            'tickets' => [
                'early' => ['name' => 'Early Bird', 'cost' => 5000, 'available' => 75],
            ],
            'discountCodes' => [
                '50off' => [
                    'type' => DiscountType\Percentage::class,
                    'options' => ['percentage' => 50],
                    'name' => '50% Off',
                    'metadata' => [
                        'availableFrom' =>(new \DateTime)->sub(new \DateInterval('P2D')),
                        'availableTo' =>(new \DateTime)->sub(new \DateInterval('P1D')),
                    ]
                ],
                '5offone' => [
                    'type' => DiscountType\Fixed::class,
                    'options' => ['net' => 500],
                    'name' => '$5 Off',
                    'metadata' => [
                        'availableFrom' =>(new \DateTime)->sub(new \DateInterval('P1D')),
                        'availableTo' =>(new \DateTime)->add(new \DateInterval('P1D')),
                    ]
                ],
                '5offall' => [
                    'type' => DiscountType\FixedPerTicket::class,
                    'options' => ['gross' => 500],
                    'name' => '$5 Off',
                ],
            ],
            'financial' => [
                'taxRate' => 10,
                'currency' => 'GBP',
                'displayTax' => true
            ]
        ]);

        $this->filters = [
            new Filters\DiscountByDate($this->config),
        ];
    }

    public function testFetchAllAvailableDiscountCodes()
    {
        $mockFinder = m::mock(RepositoryInterface::class);
        $mockFinder->shouldReceive('matching')->andReturn(new ArrayCollection($this->config->getDiscountCodes()));

        $sut = new DiscountCodeAvailability($mockFinder, ...$this->filters);

        $result = $sut->fetchAllAvailableDiscountCodes();

        self::assertTrue($result->count() === 2, 'The expected number of discount codes was not returned');
        self::assertFalse($result->contains($this->config->getDiscountCodes()['50off']), 'Expired code was included in results');
    }

    /**
     * @dataProvider provideIsAvailable
     *
     */
    public function testIsAvailable($code, $expected)
    {
        $mockFinder = m::mock(RepositoryInterface::class);
        $mockFinder->shouldReceive('matching')->andReturn(new ArrayCollection($this->config->getDiscountCodes()));

        $sut = new DiscountCodeAvailability($mockFinder, ...$this->filters);

        self::assertEquals($expected, $sut->isAvailable($code));
    }

    public function provideIsAvailable()
    {
        return [
            [$this->config->getDiscountCodes()['50off'], false],
            [$this->config->getDiscountCodes()['5offone'], true],
            [$this->config->getDiscountCodes()['5offall'], true],
        ];
    }
}
