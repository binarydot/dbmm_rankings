<?php

if (! function_exists('legacy_lang')) {
    function legacy_lang(string $key): string
    {
        static $catalogs = [];
        $locale = service('session')->get('site_lang') === 'italian' ? 'italian' : 'english';
        $catalogName = str_starts_with($key, 'title_') ? 'titles_lang' : (str_starts_with($key, 'blurbs_') ? 'blurbs_lang' : null);

        if ($catalogName === null) {
            return $key;
        }

        $catalogKey = $locale . '/' . $catalogName;
        if (! array_key_exists($catalogKey, $catalogs)) {
            $lang = [];
            $file = APPPATH . 'LanguageLegacy/' . $catalogKey . '.php';
            if (! is_file($file)) {
                throw new RuntimeException("Language catalog {$catalogKey} is missing.");
            }
            require $file;
            $catalogs[$catalogKey] = $lang;
        }

        return $catalogs[$catalogKey][$key] ?? $key;
    }
}

if (! function_exists('legacy_elapsed_time')) {
    function legacy_elapsed_time(): string
    {
        $startedAt = $_SERVER['REQUEST_TIME_FLOAT'] ?? microtime(true);

        return number_format(max(0, microtime(true) - $startedAt), 4);
    }
}

if (! function_exists('getImg')) {
    function getImg(string $rarName): string
    {
        $rar = db_connect()->query('SELECT rar_tag FROM rar WHERE rar_name = ?', [$rarName])->getRow();
        $tag = $rar->rar_tag ?? 'XI';
        $filename = $tag === 'XI' ? '_united-nations.png' : $tag . '.png';
        $path = FCPATH . 'images/flags24/' . $filename;
        if (! is_file($path)) {
            $filename = '_united-nations.png';
        }

        return '<img src="' . esc(base_url('images/flags24/' . rawurlencode($filename)), 'attr') . '" alt="' . esc($rarName, 'attr') . '">';
    }
}
