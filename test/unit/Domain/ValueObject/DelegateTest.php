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

namespace ConferenceTools\Tickets\Domain\ValueObject;

use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
final class DelegateTest extends TestCase
{
    public function testFromArray()
    {
        $data['firstname'] = 'Ed';
        $data['lastname'] = 'Nigma';
        $data['email'] = 'ed.nigma@gmail.com';
        $data['company'] = 'Mystery Inc.';
        $data['twitter'] = '@ed_nigma';
        $data['requirements'] = 'none';

        $sut = Delegate::fromArray($data);
        $this->assertSame($data['firstname'], $sut->getFirstname());
        $this->assertSame($data['lastname'], $sut->getLastname());
        $this->assertSame($data['email'], $sut->getEmail());
        $this->assertSame($data['company'], $sut->getCompany());
        $this->assertSame($data['twitter'], $sut->getTwitter());
        $this->assertSame($data['requirements'], $sut->getRequirements());
    }

    public function testEmpty()
    {
        $data['firstname'] = '';
        $data['lastname'] = '';
        $data['email'] = '';
        $data['company'] = '';
        $data['twitter'] = '';
        $data['requirements'] = '';

        $sut = Delegate::emptyObject();
        $this->assertSame($data['firstname'], $sut->getFirstname());
        $this->assertSame($data['lastname'], $sut->getLastname());
        $this->assertSame($data['email'], $sut->getEmail());
        $this->assertSame($data['company'], $sut->getCompany());
        $this->assertSame($data['twitter'], $sut->getTwitter());
        $this->assertSame($data['requirements'], $sut->getRequirements());
    }
}
