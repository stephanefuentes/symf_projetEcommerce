<?php


namespace App\Service;

use cebe\markdown\Markdown;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\CacheInterface;




class MarkdownCacheHelper{

    protected $parser;
    protected $cache;


    public function __construct(CacheInterface $cache, Markdown $parser )
    {
            $this->parser = $parser;
            $this->cache = $cache;
    }


    public function parse($content): string{

        // si 'introduction n'est pas trouvé, la function est appellé
        return $this->cache->get(md5($content), function (ItemInterface $item) use ($content) {
            return $this->parser->parse($content);
        });
        
    }


}