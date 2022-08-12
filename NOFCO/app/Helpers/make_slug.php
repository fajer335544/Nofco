<?php
/**
 * Created by PhpStorm.
 * User: amro
 * Date: 12/21/16
 * Time: 11:38 AM
 */

function make_slug($string = null, $separator = "-")
{
    if (is_null($string)) {
        return "";
    }
    $string = trim($string);
    $string = str_replace('&nbsp;', ' ', $string);
    // Lower case everything
    // using mb_strtolower() function is important for non-Latin UTF-8 string | more info: http://goo.gl/QL2tzK
    $string = mb_strtolower($string, "UTF-8");;

    // Make alphanumeric (removes all other characters)
    // this makes the string safe especially when used as a part of a URL
    // this keeps latin characters and arabic charactrs as well

    $string = str_replace(array('[\', \']'), '', $string);
    $string = htmlentities($string, ENT_COMPAT, 'utf-8');
    $string = preg_replace('/&([a-z])(acute|uml|circ|grave|ring|cedil|slash|tilde|caron|lig|quot|rsquo);/i', '\\1', $string);

    $string = preg_replace("/[^\-a-z0-9_\s-ءاأإآؤئبتثجحخدذرزسشصضطظعغفقكلمنهويةى]/u", "", $string);

    // Remove multiple dashes or whitespaces
    $string = preg_replace("/[\s-]+/", " ", $string);

    // Convert whitespaces and underscore to the given separator
    $string = preg_replace("/[\s_]/", $separator, $string);

    return $string;
}

if (!function_exists('words')) {
    /**
     * Limit the number of words in a string.
     *
     * @param  string $value
     * @param  int $words
     * @param  string $end
     * @return string
     */
    function words($value, $words = 100, $end = '...')
    {
        return \Illuminate\Support\Str::words(strip_tags($value), $words, $end);
    }
}


function get_youtube_id($url)
{
    $pattern =
        '%^# Match any youtube URL
                (?:https?://)?  # Optional scheme. Either http or https
                (?:www\.)?      # Optional www subdomain
                (?:             # Group host alternatives
                  youtu\.be/    # Either youtu.be,
                | youtube\.com  # or youtube.com
                  (?:           # Group path alternatives
                    /embed/     # Either /embed/
                  | /v/         # or /v/
                  | /watch\?v=  # or /watch\?v=
                  )             # End path alternatives.
                )               # End host alternatives.
                ([\w-]{10,12})  # Allow 10-12 for 11 char youtube id.
                $%x';
    $result = preg_match($pattern, $url, $matches);
    if ($result) {
        return $matches[1];
    }
    return '';
}

function get_breadcrumb($data)
{
    $result = '<nav aria-label="breadcrumb">';
    $result .= '<ol class="breadcrumb bg-transparent">';
    $result .= '<li  class=\'breadcrumb-item\' ><a href="' . url(App::getLocale() . '/') . '">' . trans('site.home') . '</a> </li>';
    if(is_array($data))
    {
        foreach ($data as $key=> $val)
        {
            $result .= "<li class='breadcrumb-item'>".$val."</li>";
        }
    }
    $result .= '</ol>';
    $result .= '</nav>';
    return $result;
}

function remove_protocol($url){
    return preg_replace("(^https?://)", "", $url );
}
