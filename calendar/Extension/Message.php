<?php

namespace Extension;


class Message {

    public static function loadMessageFile($file_path) {
        return include $file_path;
    }

    public static function findMessageFile($file) {
        $PHP_EXT = '.php';
        $MESSAGES_PATH = APPROOT . 'messages/';
        $find_path = $MESSAGES_PATH . $file . $PHP_EXT;
        if (is_file($find_path))
            return $find_path;
        return null;
    }

    public static function message($file, $path = NULL, $default = NULL) {

        static $messages;

        if ( ! isset($messages[$file]))
        {
            // Create a new message list
            $messages[$file] = array();

            if ($found = self::findMessageFile($file))
            {
                $messages[$file] = Arr::merge($messages[$file], self::loadMessageFile($found));
            }
        }

        if ($path === NULL)
        {
            // Return all of the messages
            return $messages[$file];
        }
        else
        {
            // Get a message using the path
            return Arr::path($messages[$file], $path, $default);
        }
    }
}
