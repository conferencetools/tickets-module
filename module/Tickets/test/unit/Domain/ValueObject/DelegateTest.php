<?php

namespace ConferenceTools\Tickets\Domain\ValueObject;

use PHPUnit\Framework\TestCase;

class DelegateTest extends TestCase
{
    public function testFromArray()
    {
        $data['firstname']= 'Ed';
        $data['lastname'] = 'Nigma';
        $data['email'] = 'ed.nigma@gmail.com';
        $data['company'] = 'Mystery Inc.';
        $data['twitter'] = '@ed_nigma';
        $data['requirements'] = 'none';

        $sut = Delegate::fromArray($data);
        self::assertEquals($data['firstname'], $sut->getFirstname());
        self::assertEquals($data['lastname'], $sut->getLastname());
        self::assertEquals($data['email'], $sut->getEmail());
        self::assertEquals($data['company'], $sut->getCompany());
        self::assertEquals($data['twitter'], $sut->getTwitter());
        self::assertEquals($data['requirements'], $sut->getRequirements());
    }

    public function testEmpty()
    {
        $data['firstname']= '';
        $data['lastname'] = '';
        $data['email'] = '';
        $data['company'] = '';
        $data['twitter'] = '';
        $data['requirements'] = '';

        $sut = Delegate::emptyObject();
        self::assertEquals($data['firstname'], $sut->getFirstname());
        self::assertEquals($data['lastname'], $sut->getLastname());
        self::assertEquals($data['email'], $sut->getEmail());
        self::assertEquals($data['company'], $sut->getCompany());
        self::assertEquals($data['twitter'], $sut->getTwitter());
        self::assertEquals($data['requirements'], $sut->getRequirements());
    }
}
