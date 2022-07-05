<?php
namespace Custom\Helper;

class Url
{
    public static function getCrmEntityByUrl($page)
    {
        if(strpos($page, 'lead') !== false) {
            return ['TYPE' => 'lead', 'ID' => str_replace(['/crm/lead/details/', '/'], '', $page)];
        } elseif(strpos($page, 'deal') !== false) {
            return ['TYPE' => 'deal', 'ID' => str_replace(['/crm/deal/details/', '/'], '', $page)];
        } elseif(strpos($page, 'contact') !== false) {
            return ['TYPE' => 'contact', 'ID' => str_replace(['/crm/contact/details/', '/'], '', $page)];
        } elseif(strpos($page, 'company') !== false) {
            return ['TYPE' => 'company', 'ID' => str_replace(['/crm/company/details/', '/'], '', $page)];
        } else {
            return false;
        }
    }
}
