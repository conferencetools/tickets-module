<?php

namespace OpenTickets\Tickets\View\Helper;

use Zend\View\Helper\FlashMessenger as ZendFlashMessenger;
use Zend\Mvc\Controller\Plugin\FlashMessenger as PluginFlashMessenger;

class FlashMessenger extends ZendFlashMessenger
{
    protected $classMessages = array(
        PluginFlashMessenger::NAMESPACE_INFO => 'alert-info',
        PluginFlashMessenger::NAMESPACE_ERROR => 'alert-danger',
        PluginFlashMessenger::NAMESPACE_SUCCESS => 'alert-success',
        PluginFlashMessenger::NAMESPACE_DEFAULT => 'alert-info',
        PluginFlashMessenger::NAMESPACE_WARNING => 'alert-warning',
    );

    public function renderAll()
    {
        $flashMessenger = $this->getPluginFlashMessenger();
        $markup = '';
        foreach ($this->classMessages as $namespace => $class) {
            $messages = $flashMessenger->getMessagesFromNamespace($namespace);
            $markup .= $this->renderMessages($namespace, $messages);
        }

        return $markup;
    }

    protected function renderMessages(
        $namespace = PluginFlashMessenger::NAMESPACE_DEFAULT,
        array $messages = array(),
        array $classes = array(),
        $autoEscape = null
    ) {
        // Prepare classes for opening tag
        if (empty($classes)) {
            if (isset($this->classMessages[$namespace])) {
                $classes = $this->classMessages[$namespace];
            } else {
                $classes = $this->classMessages[PluginFlashMessenger::NAMESPACE_DEFAULT];
            }
            $classes = array($classes);
        }

        if (null === $autoEscape) {
            $autoEscape = $this->getAutoEscape();
        }
        $messagesToPrint = $this->flattenMessages($messages, $autoEscape);

        // Generate markup
        $markup = '';
        foreach ($messagesToPrint as $message) {
            $markup .= $this->getView()->alert($message, ['class' => current($classes)], true);
        }

        return $markup;
    }

    /**
     * @param array $messages
     * @param $autoEscape
     * @return array
     */
    private function flattenMessages(array $messages, $autoEscape)
    {
        // Flatten message array
        $escapeHtml = $this->getEscapeHtmlHelper();
        $messagesToPrint = array();
        $translator = $this->getTranslator();
        $translatorTextDomain = $this->getTranslatorTextDomain();
        array_walk_recursive(
            $messages,
            function ($item) use (& $messagesToPrint, $escapeHtml, $autoEscape, $translator, $translatorTextDomain) {
                if ($translator !== null) {
                    $item = $translator->translate(
                        $item,
                        $translatorTextDomain
                    );
                }

                if ($autoEscape) {
                    $messagesToPrint[] = $escapeHtml($item);
                    return;
                }

                $messagesToPrint[] = $item;
            }
        );
        return $messagesToPrint;
    }
}