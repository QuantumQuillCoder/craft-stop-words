<?php

namespace quantumquillcoder\craftstopwords\langs;

class LanguageManager
{
    public static function getMessages($language): array
    {
        $languageFile = __DIR__ . '/' . $language . '.php';
        return file_exists($languageFile) ? include $languageFile : [];
    }
}
