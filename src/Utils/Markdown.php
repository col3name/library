<?php

namespace App\Utils;

/**
 * Class Markdown
 * @package App\Utills
 */
class Markdown
{
    private $parse;

    /**
     * Markdown constructor.
     */
    public function __construct()
    {
        $this->parse = new \Parsedown();
    }

    /**
     * @param string $text
     * @return string
     */
    public function toHtml(string $text)
    {
        return $this->parse->text($text);
    }
}