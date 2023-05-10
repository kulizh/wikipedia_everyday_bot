<?php
namespace Unclebot\Wikipedia;

class Parser
{   
    private static function getHost(string $lang): string
    {
        return 'https://' . $lang . '.wikipedia.org';
    }

    public static function getRandomUrl(string $lang = 'ru'): string
    {
        $url = self::getHost($lang) . '/w/api.php?format=json&action=query&generator=random&grnlimit=1&grnnamespace=0';

        $json = file_get_contents($url);
        
        if (empty($json))
        {
            return '';
        }

        $data = json_decode($json, true);
        
        $article_title = reset($data['query']['pages'])['title'] ?? '';

        if (empty($article_title))
        {
            return '';
        }

        return self::getHost($lang) . '/wiki/' . str_replace(' ', '_', $article_title);
    }
}