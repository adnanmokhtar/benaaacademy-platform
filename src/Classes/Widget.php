<?php

namespace Benaaacademy\Platform\Classes;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Event;

/*
 * Class BenaaacademyWidget
 * @package Benaaacademy\Platform
 */
class Widget
{

    /*
     * @var array
     */
    public static $widgets = [];

    /*
     * @var
     */
    public static $widget;

    /*
     * @param $sidebar
     * @return string
     * Render sidebar widgets
     */
    public static function render($sidebar)
    {

        $args = func_get_args();
        $args = array_splice($args, 1);

        $widgets = self::get($sidebar, $args);

        $output_html = "";

        foreach ($widgets as $widget) {
            array_unshift($args, $widget);
            $output_html .= call_user_func_array($widget->output, $args);
        }

        return $output_html;

    }

    /*
     * @param $sidebar
     * @return array
     * get ordered widgets of a given sidebar
     */
    public static function get($sidebar, $args)
    {

        Event::dispatch($sidebar . ".widget", $args);

        $return = [];

        foreach (self::$widgets as $widget) {
            if ($widget->sidebar == $sidebar) {
                $return[] = $widget;
            }
        }

        $widgets_collection = new Collection($return);
        $sorted_widgets = $widgets_collection->sortBy("order")->toArray();

        return $sorted_widgets;

    }

    /*
     * @return array
     * return all widgets of all sidebars
     */
    public static function all()
    {
        return self::$widgets;
    }

    /*
     * @param $sidebar
     * @param bool $callback
     * @return Widget
     * Making of widget object
     */
    public static function sidebar($sidebar, $callback = false)
    {

        if ($callback) {
            Event::listen($sidebar . ".widget", function () use ($sidebar, $callback) {

                self::$widget = new self();

                self::$widget->sidebar = $sidebar;
                self::$widget->name = "";
                self::$widget->order = 0;
                self::$widget->output = "";

                if (is_callable($callback)) {
                    self::$widget->output = $callback;
                }

                self::$widgets[] = self::$widget;

            });

        }


    }

    /*
     * @param string $name
     * @return mixed
     * set name of widget if wanted
     */
    function name($name = "")
    {
        self::$widget->name = $name;
        return self::$widget;

    }

    /*
     * @param int $order
     * @return mixed
     * set order of widget if wanted
     */
    function order($order = 0)
    {
        self::$widget->order = $order;
        return self::$widget;
    }


    /*
     * @param $callback
     * @return mixed
     * set output of widget (required)
     */
    function set($callback)
    {
        if (is_callable($callback)) {
            self::$widget->output = $callback();
        } else {
            self::$widget->output = $callback;
        }
    }


}
