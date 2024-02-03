<?php

/*
 * @return Dot instance
 */
function dot($make = NULL)
{

    if (is_null($make)) {
        return Dot::getInstance();
    }

    return Dot::getInstance()->get($make);
}

/*
 * @return float
 * return current cms version
 */
function cms_version()
{
    return CMS_VERSION;
}

/*
 * @param string $file
 * @return mixed
 */
function admin_url($file = "")
{
    return URL::to(ADMIN . "/" . $file);
}

/*
 * @param string $path
 * @return string
 */
function assets($path = "")
{

    if (strstr($path, "::")) {
        list($namespace, $path) = explode("::", $path);
        return asset("plugins/" . $namespace . "/" . $path);
    }

    return asset($path);
}

/*
 * @param $title
 * @param string $separator
 * @return mixed
 */
function str_slug_utf8($title, $separator = '-')
{
    return Str::slug_utf8($title, $separator);
}

/*
 * @return mixed
 */
function get_locales()
{
    return Config::get("admin.locales");
}

/*
 * Since Post Date
 *
 * @return Response
 */
function time_ago($date, $granularity = 2)
{

    if (!is_numeric($date)) {
        $date = strtotime($date);
    }

    $difference = 0;

    while (empty($difference)) {
        $difference = time() - $date;
    }

    $periods = array(
        trans("admin::common.century") => 315360000,
        trans("admin::common.year") => 31536000,
        trans("admin::common.month") => 2628000,
        trans("admin::common.week") => 604800,
        trans("admin::common.day") => 86400,
        trans("admin::common.hour") => 3600,
        trans("admin::common.minute") => 60,
        trans("admin::common.second") => 1
    );

    $retval = '';

    foreach ($periods as $key => $value) {
        if ($difference >= $value) {
            $time = floor($difference / $value);
            $difference %= $value;
            $retval .= ($retval ? ' ' : '') . $time . ' ';
            $retval .= $key;
            $granularity--;
        }
        if ($granularity == '0') {
            break;
        }
    }

    return $retval;

}

/*
 * @param $date
 * @return mixed|string
 */
function date_view($date)
{
    if (DIRECTION == 'rtl') {
        return arabic_date($date);
    } else {
        return date('M d, Y', strtotime($date)) . '@' . date('h:i a', strtotime($date));
    }
}

/*
 * @param string $target_date
 * @param string $type
 * @return mixed
 */
function arabic_date($target_date = '', $type = '')
{

    // PHP Arabic Date

    if ($type != 'mongo') {
        $target_date = strtotime($target_date);
    }

    $months = array(
        "Jan" => "يناير",
        "Feb" => "فبراير",
        "Mar" => "مارس",
        "Apr" => "أبريل",
        "May" => "مايو",
        "Jun" => "يونيو",
        "Jul" => "يوليو",
        "Aug" => "أغسطس",
        "Sep" => "سبتمبر",
        "Oct" => "أكتوبر",
        "Nov" => "نوفمبر",
        "Dec" => "ديسمبر"
    );

    if (!$target_date) {

        $target_date = date('y-m-d'); // The Current Date
    }

    $en_month = date("M", $target_date);

    foreach ($months as $en => $ar) {
        if ($en == $en_month) {
            $ar_month = $ar;
            break;
        }
    }

    $find = array(
        "Sat",
        "Sun",
        "Mon",
        "Tue",
        "Wed",
        "Thu",
        "Fri"
    );

    $replace = array(
        "السبت",
        "الأحد",
        "الإثنين",
        "الثلاثاء",
        "الأربعاء",
        "الخميس",
        "الجمعة"
    );

    $ar_day_format = date('D', $target_date); // The Current Day

    $ar_day = str_replace($find, $replace, $ar_day_format);

    header('Content-Type: text/html; charset=utf-8');

    $standard = array("0", "1", "2", "3", "4", "5", "6", "7", "8", "9");
    $eastern_arabic_symbols = array("٠", "١", "٢", "٣", "٤", "٥", "٦", "٧", "٨", "٩");
    $standard2 = array("pm", "PM", "am", "AM");
    $eastern_arabic_symbols2 = array("م", "م", "ص", "ص");
    $the_date = $ar_day . '، ' . date('j', $target_date) . ' ' . $ar_month . ' ' . date('Y', $target_date) . ' - ' . date('g:i a', $target_date);
    $arabic_date = str_replace($standard, $eastern_arabic_symbols, $the_date);
    $arabic_date = str_replace($standard2, $eastern_arabic_symbols2, $arabic_date);

    // Echo Out the Date
    return $arabic_date;
}


function denied()
{
    return response(view("admin::errors.denied")->render(), 403);
}


function get_relative_path($value = '')
{
    return str_replace(base_path(), "", $value);
}

function plugin($name)
{
    return \Dot\Platform\Facades\Plugin::get($name);
}
