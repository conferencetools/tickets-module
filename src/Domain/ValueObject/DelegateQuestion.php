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

use JMS\Serializer\Annotation as JMS;

class DelegateQuestion
{
    /**
     * @var string
     * @JMS\Type("string")
     */
    private $handle;

    /**
     * @var string
     * @JMS\Type("string")
     */
    private $answer;

    /**
     * DelegateQuestion constructor.
     *
     * @param $handle
     * @param $answer
     */
    public function __construct(string $handle, string $answer)
    {
        $this->handle = $handle;
        $this->answer = $answer;
    }

    /**
     * @return string
     */
    public function getHandle(): string
    {
        return $this->handle;
    }

    /**
     * @return string
     */
    public function getAnswer(): string
    {
        return $this->answer;
    }
}
