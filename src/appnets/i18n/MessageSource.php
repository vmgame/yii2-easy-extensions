<?php
/**
 * Created by PhpStorm.
 * User: Bao Chung
 * Date: 4/4/14
 * Time: 3:19 PM
 */

namespace appnets\i18n;

use yii\i18n\MissingTranslationEvent;
use yii\i18n\PhpMessageSource;

class MessageSource extends PhpMessageSource{
    private $_messages=array();

    protected function translateMessage($category, $message, $language) {
        $key = $language . '.' . $category;

        if (!isset($this->_messages[$key]))
            $this->_messages[$key] = $this->loadMessages($category, $language);

        if (isset($this->_messages[$key][$message]) && $this->_messages[$key][$message] !== '')
            return $this->_messages[$key][$message];

        $this->_messages[$key][$message] = $message;
        $file = $this->getMessageFilePath($category, $language);
        if (!is_dir(dirname($file))) {
            @mkdir(dirname($file), 0755, true);
        }

        @file_put_contents($file, '<?php return ' . var_export((array) $this->_messages[$key], true) . ';');

        if ($this->hasEventHandlers('onMissingTranslation')) {
            $event = new MissingTranslationEvent($this, $category, $message, $language);
            $this->on('onMissingTranslation',$event);
            return $event->message;
        }
        else
            return $message;
    }
} 