<?php

namespace Dot\Platform\Classes;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Request;

/*
 * Class Menu
 */
class Menu
{

    /*
     * @var array
     */
    public static $menus = [];

    /*
     * @var
     */
    public static $menu;

    /*
     * @var
     */
    public $current;

    public function __construct()
    {
        $this->current = Request::url();
    }

    /*
     * @param $sidebar
     * @return string
     * Render sidebar menus
     */
    public static function render($sidebar, $parent = "master", $level = 0, $calls = 0)
    {
        $items = self::get($sidebar, $parent);

        // $calls++;

        if ($level <= 2) {
            $attr = array(
                'class' => 'nav nav-second-level collapse level-' . $level
            );
        } else {
            $attr = array(
                'class' => 'nav nav-third-level collapse level-' . $level
            );
        }

        $menu = "";
        // if ($level != 1) {
        $menu = '<ul' . HTML::attributes($attr) . '>';
        // }

        if (count($items)) {

            $level++;

            foreach ($items as $item) {

                $classes = array();
                $classes[] = self::getActive($item);

                $has_children = count($item->children);

                $classes[] = "lev-" . $level;

                $menu .= '<li' . HTML::attributes(array('class' => implode(' ', $classes))) . '>';

                $menu .= self::createAnchor($item, $level, $has_children);

                if ($has_children) {
                    $menu .= self::render($sidebar, $item->name, $level);
                }

                $menu .= '</li>';
            }
        } else {
            $level--;
        }

        $menu .= '</ul>';

        return $menu;
    }

    /*
     * @param $sidebar
     * @return array
     * get ordered menus of a given sidebar
     */
    public static function get($sidebar, $parent = "master", $calls = 0)
    {

        if ($calls == 0) {
            Event::dispatch($sidebar . ".menu");
            $calls++;
        }

        $return = [];
        foreach (self::$menus as $menu) {
            if ($menu->sidebar == $sidebar) {
                $return[] = $menu;
            }
        }

        $menus_collection = new Collection($return);

        $filtered_items = $menus_collection->filter(function ($item) use ($parent) {
            return $item->parent == $parent;
        });

        $sorted_items = $filtered_items->sortBy("order")->toArray();

        $all_widgets = [];

        foreach ($sorted_items as $item) {
            $item->children = self::get($sidebar, $item->name, $calls);
            $all_widgets[] = $item;
        }

        return $all_widgets;
    }

    private static function getActive($item)
    {
        return "";
        $url = trim($item->url, '/');

        if (self::$current === $url) {
            return 'active current';
        }

        /*  if (strpos($this->currentKey, $item->name) === 0) {
              return 'active';
          }*/
    }

    private static function createAnchor($item, $level, $has_children)
    {

        $output = '<a href="' . $item->url . '">';
        $output .= self::createIcon($item);

        if ($level <= 2) {
            $output .= '<span class="nav-label">' . $item->title . '</span>';
        } else {
            $output .= $item->title;
        }

        if ($has_children) {
            $output .= '<span class="fa arrow"></span>';
        }
        $output .= '</a>';

        return $output;
    }

    private static function createIcon($item)
    {

        $output = '';
        if ($item->icon != "") {
            $output .= '<i class="fa ' . $item->icon . '"></i>';
        }

        return $output;
    }

    /*
     * @param $key
     * @return string
     */
    public static function lang($key)
    {
        return isset(self::$langs[$key]) ? self::$langs[$key] : "-";
    }

    /*
     * @return array
     * return all menus of all sidebars
     */
    public static function all()
    {
        return self::$menus;
    }

    /*
     * @param $sidebar
     * @param bool $callback
     * @return Menu
     * Making of menu object
     */
    public static function sidebar($sidebar, $callback = false)
    {
        if ($callback) {
            Event::listen($sidebar . ".menu", function () use ($sidebar, $callback) {

                self::$menu = new self();

                self::$menu->sidebar = $sidebar;
                self::$menu->name = "";
                self::$menu->title = "";
                self::$menu->url = "javascript:void(0)";
                self::$menu->parent = "master";
                self::$menu->order = 0;
                self::$menu->icon = "";
                self::$menu->children = [];
                self::$menu->output = "";

                if (is_callable($callback)) {
                    $output = $callback(self::$menu);
                    if ($output) {
                        self::$menu->output = $output;
                    }
                } else {
                    self::$menu->output = $callback;
                }

                self::$menus[] = self::$menu;

            });
        }
    }

    /*
     * @param string $name
     * @return mixed
     * set name of menu if wanted
     */
    function name($name = "")
    {

        self::$menu->name = $name;
        self::$menus[self::$menu->name] = $name;
        return self::$menu;
    }

    /*
     * @param string $name
     * @return mixed
     * set name of menu if wanted
     */
    function url($url = "javascript:void(0)")
    {
        self::$menu->url = $url;
        self::$menus[self::$menu->url] = $url;
        return self::$menu;
    }

    /*
     * @param string $name
     * @return mixed
     * set parent of item if wanted
     */
    function parent($name = "master")
    {
        self::$menu->parent = $name;
        return self::$menu;

    }

    /*
     * @param string $title
     * @return mixed
     * set title of item if wanted
     */
    function title($title = "")
    {
        self::$menu->title = $title;
        return self::$menu;
    }

    /*
     * @param string $icon
     * @return mixed
     * set icon of item if wanted
     */
    function icon($icon = "")
    {
        self::$menu->icon = $icon;
        return self::$menu;
    }

    /*
     * @param int $order
     * @return mixed
     * set order of menu if wanted
     */
    function order($order = 0)
    {
        self::$menu->order = $order;
        return self::$menu;
    }

    /*
     * @param array []
     * @return mixed
     * set children items of item if wanted
     */
    function children($children = [])
    {
        self::$menu->children = $children;
        return self::$menu;
    }


    /*
     * @param $callback
     * @return mixed
     * set output of menu (required)
     */
    function set($callback)
    {
        if (is_callable($callback)) {
            self::$menu->output = $callback();
        } else {
            self::$menu->output = $callback;
        }
    }


}
