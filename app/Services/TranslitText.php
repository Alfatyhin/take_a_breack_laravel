<?php


namespace App\Services;


class TranslitText
{
    private $translitArray = array(
        'а' => 'a',    'б' => 'b',    'в' => 'v',    'г' => 'g',    'д' => 'd',
        'е' => 'e',    'ё' => 'yuo',    'ж' => 'zh',   'з' => 'z',    'и' => 'i',
        'й' => 'y',    'к' => 'k',    'л' => 'l',    'м' => 'm',    'н' => 'n',
        'о' => 'o',    'п' => 'p',    'р' => 'r',    'с' => 's',    'т' => 't',
        'у' => 'u',    'ф' => 'f',    'х' => 'h',    'ц' => 'c',    'ч' => 'ch',
        'ш' => 'sh',   'щ' => 'sch',  'ь' => 'jh',   'ы' => 'ui',   'ъ' => 'gh',
        'э' => 'he',    'ю' => 'yu',   'я' => 'ya',

        'А' => 'A',    'Б' => 'B',    'В' => 'V',    'Г' => 'G',    'Д' => 'D',
        'Е' => 'E',    'Ё' => 'E',    'Ж' => 'Zh',   'З' => 'Z',    'И' => 'I',
        'Й' => 'Y',    'К' => 'K',    'Л' => 'L',    'М' => 'M',    'Н' => 'N',
        'О' => 'O',    'П' => 'P',    'Р' => 'R',    'С' => 'S',    'Т' => 'T',
        'У' => 'U',    'Ф' => 'F',    'Х' => 'H',    'Ц' => 'C',    'Ч' => 'Ch',
        'Ш' => 'Sh',   'Щ' => 'Sch',  'Ь' => 'JH',   'Ы' => 'UI',   'Ъ' => 'GH',
        'Э' => 'E',    'Ю' => 'Yu',   'Я' => 'Ya',
    );

    public function Translit($str)
    {
        $converter = $this->translitArray;
        return strtr($str, $converter);
    }
    public function ReTranslit($str)
    {
        $converter = array_flip($this->translitArray);
        return strtr($str, $converter);
    }

    public function TranslitFileName($str)
    {
        $str = trim($str); // убираем пробелы в начале и конце строки
        $str = strip_tags($str); // убираем HTML-теги
        $str = str_replace(array("\n", "\r"), " ", $str); // убираем перевод каретки
        $str = $this->Translit($str);
        $str = preg_replace("/[^0-9a-z-_ ]/i", "", $str); // очищаем строку от недопустимых символов
        $str = preg_replace("/\s+/", ' ', $str); // удаляем повторяющие пробелы
        $str = str_replace(' ', '_', $str);

        return $str;
    }
}
