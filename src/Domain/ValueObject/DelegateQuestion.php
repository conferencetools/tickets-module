<?php

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
