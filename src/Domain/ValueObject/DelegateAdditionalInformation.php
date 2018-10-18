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
