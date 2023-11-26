<?php

namespace quantumquillcoder\craftstopwords\helpers;

use craft\helpers\StringHelper;
use quantumquillcoder\craftstopwords\langs\LanguageManager;

class SlugHelper extends LanguageManager
{
    public static function removeStopWords(string $dirtyString, string $lang = 'en'): string
    {
        $stopWords = LanguageManager::getMessages($lang);
        $dirtyString = '-' . StringHelper::slugify($dirtyString) . '-';
        $cleanSlug = str_replace($stopWords, '-', $dirtyString);
        return StringHelper::slugify($cleanSlug);
    }
}
