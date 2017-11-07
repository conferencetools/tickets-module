<?php

namespace ConferenceTools\Tickets\Domain\ValueObject;

use JMS\Serializer\Annotation as JMS;

class DelegateAdditionalInformation
{
    /**
     * @var DelegateQuestion[]
     * @JMS\Type("array<ConferenceTools\Tickets\Domain\ValueObject\DelegateQuestion>")
     */
    private $questions = [];

    public function addQuestion(string $handle, string $answer)
    {
        $this->questions[] = new DelegateQuestion($handle, $answer);
    }

    /**
     * @return DelegateQuestion[]
     */
    public function getQuestions()
    {
        return $this->questions;
    }
}