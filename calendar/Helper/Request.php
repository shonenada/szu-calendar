<?php

namespace Helper;

class Request {
    static public function parseBody($rawBody) {
        $output = array();
        if (!isset($rawBody) || strlen($rawBody) < 1) {
            return $output;
        }
        $kvs = explode('&', $rawBody);
        foreach ($kvs as $kv) {
            list($key, $value) = explode('=', $kv);
            if (!isset($output[$key])) {
                $output[$key] = $value;
                continue;
            }
            if (isset($output[$key]) && is_string($output[$key])) {
                $output[$key] = array($output[$key]);
            }
            $output[$key][] = $value;
        }
        return $output;
    }
}
