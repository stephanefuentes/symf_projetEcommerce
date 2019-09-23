<?php

namespace App\Twig;

use Twig\TwigFilter;
use App\Service\MarkdownCacheHelper;
use Twig\Extension\AbstractExtension;


// le tag ['twig.extension'] sera mis automatiquement sur notre classe 
class MarkdownExtension extends AbstractExtension
{


    protected $helper;


    public function __construct(MarkdownCacheHelper $helper)
    {
        $this->helper = $helper;
    }


    public function getFilters()
    {
        return [
            new TwigFilter('markdown',[$this, 'parseMarkdown'], ['is_safe' => ['html'] ])
        ];
    }


    public function parseMarkdown($content)
    {
        return $this->helper->parse($content);
    }


}

