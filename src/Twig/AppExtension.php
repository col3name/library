<?php

namespace App\Twig;

use App\Utils\Markdown;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * Class AppExtension
 * @package App\Twig
 */
class AppExtension extends AbstractExtension
{
    private $parser;

    /**
     * AppExtension constructor.
     */
    public function __construct()
    {
        $this->parser = new Markdown();
    }

    /**
     * @return array|\Twig_Filter[]
     */
    public function getFilters()
    {
        return [
            new TwigFilter('toHtml', [$this, 'toHtml'], ['is_safe' => ['html']])
        ];
    }

    /**
     * @param string $text
     * @return string
     */
    public function toHtml(string $text) : string
    {
        return $this->parser->toHtml($text);
    }

}