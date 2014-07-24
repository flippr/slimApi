<?php

class Cleaner
{
    public function stripChars($value)
    {
        $input = str_replace('"', ' ', $value);
        return $input;
    }

    public function json($data)
    {
        if (is_array($data))
        {
            return json_encode($data);
        }
    }
} 