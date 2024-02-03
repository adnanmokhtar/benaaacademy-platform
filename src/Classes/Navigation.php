<?php

namespace Benaaacademy\Platform\Classes;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Request;
use View;

/*
 * Class Navigation
 * @package Benaaacademy\Platform\Classes
 */
class Navigation
{

    /*
     * @var bool
     */
    protected static $menu = false;
    /*
     * @var array
     */
    protected static $lists = [];
    /*
     * @var string
     */
    protected static $template = "";
    /*
     * @var array
     */
    protected static $langs = [];
    /*
     * @var
     */
    protected $items;
    /*
     * @var array
     */
    protected $waiting_items = [];
    /*
     * @var
     */
    protected $current;
    /*
     * @var
     */
    protected $currentKey;
    /*
     * @var bool
     */
    protected $current_key = false;

    /*
     * Menu constructor.
     */
    public function __construct()
    {
        $this->current = Request::url();
    }

    /*
     * Shortcut method for create a menu with a callback.
     * This will allow you to do things like fire an even on creation.
     *
     * @param callable $callback Callback to use after the menu creation
     * @return object
     */

    /*
     * @param $menu_name
     * @return Menu
     */
    public static function get($menu_name)
    {
        $menu = new self();
        self::$menu = $menu_name;
        Event::dispatch($menu_name . '.build', $menu);
        if ($menu_name == "sidebar") {
            $menu->sortItems();
        } else {
            $template = "";
            foreach (self::$lists as $list) {
                $template .= View::make($list["name"], $list["data"])->render();
            }
            self::$template = $template;
        }
        return $menu;
    }

    /*
     * Usage: Menu::set("topnav", function($menu){
     *      $menu->make("notification::menu");
     * });
     *
     * @param $menu
     * @param $callback
     */

    private function sortItems()
    {

        $this->items = $this->items ? $this->items : [];

        $this->items = array_merge_recursive($this->items, $this->waiting_items);

        if (Config::has("sidebar") and Config::get("sidebar") != "") {

            // sorting by database

            $orders = Config::get("sidebar");
            $orders = json_decode($orders, true);
            $this->items = $orders;
        } else {

            // sorting by code

            usort($this->items, function ($a, $b) {
                if (!isset($a['sort']) or !isset($b['sort'])) {
                    return 0;
                }
                if ($a['sort'] == $b['sort']) {
                    return 0;
                }
                return ($a['sort'] < $b['sort'] ? -1 : 1);
            });
        }

        return $this->items;
    }

    public static function menu($menu, $callback)
    {
        if ($menu == "sidebar") {
            Event::listen($menu . '.build', function ($menu) use ($callback) {
                $callback($menu);
            });
        } elseif ($menu == "topnav") {
            Event::listen($menu . '.build', function ($menu) use ($callback) {
                $callback(new self);
            });
        }
    }

    /*
     * @param bool $view
     * @param array $data
     */
    public function make($view = false, $data = [])
    {

        $list = [
            "name" => $view,
            "data" => $data
        ];

        self::$lists[] = $list;
    }

    /*
     * Add a menu item to the item stack
     *
     * @param string $key Benaaacademy separated hierarchy
     * @param string $name Text for the anchor
     * @param string $url URL for the anchor
     * @param integer $sort Sorting index for the items
     * @param string $icon URL to use for the icon
     * @return $this
     */

    /*
     * @param $name
     * @param $value
     * @return $this|array
     */
    function with($name, $value)
    {
        return self::$lists;
    }

    public function item($key, $name, $url, $sort = 0, $icon = null)
    {

        self::$langs[$key] = $name;

        $this->current_key = $key;

        $item = array(
            'key' => $key,
            'name' => ucfirst($name),
            'url' => $url,
            'sort' => $sort,
            'icon' => $icon,
            'children' => array()
        );

        $children = str_replace('.', '.children.', $key);

        if (strstr($key, ".")) {
            $parent = preg_replace("/\.[^.]+$/", "", $key);
            if (!array_has($this->items, $parent)) {
                array_set($this->waiting_items, $children, $item);
                return $this;
            }
        }

        array_set($this->items, $children, $item);

        if ($url == $this->current) {
            $this->currentKey = $key;
        }

        return $this;
    }

    function permission()
    {


        unset($this->items[$this->currentKey]);
        unset($this->items[$this->currentKey]);


        return $this;
    }

    /*
     * @param int $order
     * @return $this
     */
    public function order($order = 0)
    {
        if ($this->current_key) {
            $children = str_replace('.', '.children.', $this->current_key);
            if (array_has($this->waiting_items, $children)) {
                array_set($this->waiting_items, $children . ".sort", $order);
            } else {
                array_set($this->items, $children . ".sort", $order);
            }
        }
        return $this;
    }

    /*
     * @param null $icon
     * @return $this
     */
    public function icon($icon = null)
    {
        if ($this->current_key) {
            $children = str_replace('.', '.children.', $this->current_key);
            if (array_has($this->waiting_items, $children)) {
                array_set($this->waiting_items, $children . ".icon", $icon);
            } else {
                array_set($this->items, $children . ".icon", $icon);
            }
        }
        return $this;
    }

    /*
     * @return mixed
     */
    public function items()
    {
        return $this->items;
    }

    /*
     * Recursive function to loop through items and create a menu
     * @param array $items List of items that need to be rendered
     * @param boolean $level Which level you are currently rendering
     * @return string
     */
    public function render($items = null, $level = 1)
    {

        if (self::$menu == "topnav") {
            return self::$template;
        }

        $items = $items ?: $this->items;

        if ($level <= 2) {
            $attr = 'class="nav nav-second-level collapse level-' . $level . '"';
        } else {
            $attr = 'class="nav nav-third-level collapse level-' . $level . '"';
        }

        $menu = "";

        if ($level != 1) {
            $menu = '<ul ' . $attr . ' >';
        }

        if (count($items)) {

            $level++;

            foreach ($items as $item) {

                $classes = [];

                $item['children'] = isset($item['children']) ? $item['children'] : array();

                $has_children = sizeof($item['children']);

                $classes[] = "lev-" . $level;

                $menu .= '<li' . ' class = "' . $item["key"]."-menu-item " . implode(' ', $classes) . ' ">';

                $menu .= $this->createAnchor($item, $level, $has_children);

                if ($has_children) {
                    $menu .= $this->render($item['children'], $level);
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
     * Method to render an anchor
     * @param array $item Item that needs to be turned into a link
     * @param $level
     * @param $has_children
     * @return string
     */
    private function createAnchor($item, $level, $has_children)
    {

        if (!isset($item["url"])) {
            $item["url"] = "";
        }

        if (!isset($item["icon"])) {
            $item["icon"] = "";
        }

        if (!isset($item["name"])) {
            $item["name"] = "";
        }

        if (!isset($item["key"])) {
            $item["key"] = "";
        }

        $output = '<a href="' . $item['url'] . '">';
        $output .= $this->createIcon($item);
        if ($level <= 2) {
            $output .= '<span class="nav-label">' . ucfirst(self::lang($item["key"])) . '</span>';
        } else {
            $output .= ucfirst(self::lang($item["key"]));
        }
        if ($has_children) {
            $output .= '<span class="fa arrow"></span>';
        }
        $output .= '</a>';

        return $output;
    }

    /*
     * Method to render an icon
     * @param array $item Item that needs to be turned into a icon
     * @return string
     */
    private function createIcon($item)
    {

        $output = '';

        if (is_array($item['icon'])) {
            $item["icon"] = end($item["icon"]);
        }

        if ($item['icon'] != null) {
            $output .= '<i class="fa ' . $item['icon'] . '"></i>';
        }

        return $output;
    }

    /*
     * Method to sort through the menu items and put them in order
     * @return array|mixed
     */

    /*
     * @param $key
     * @return string
     */
    public static function lang($key)
    {
        return isset(self::$langs[$key]) ? self::$langs[$key] : "-";
    }
}
