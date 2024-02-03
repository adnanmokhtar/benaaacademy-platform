<?php

namespace Benaaacademy\Platform\Controllers;

use Benaaacademy\Platform\Controller;
use Illuminate\Support\Facades\Request;

/*
 * Description of SidebarController
 *
 * @author basem
 */
class SidebarController extends Controller
{

    /*
     * @var array
     */
    public $data = [];

    /*
     * @param $items
     * @return array
     */
    function orders($items)
    {

        static $keys = [];

        $order = 1;
        foreach ($items as $item) {
            $keys[$item->id] = $order;

            if (isset($item->children) and count($item->children)) {
                $this->orders($item->children);
            }

            $order++;
        }

        return $keys;
    }


    /*
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    function index()
    {


        //return Menu::get("sidebar")->items();


//        $items = array(
//            array("type" => "pork", "sort" => 5.43, "children" => array(
//                    array("type" => "aaa", "sort" => 9),
//                    array("type" => "bbb", "sort" => 2, "children" => array(
//                            array("type" => "qqq", "sort" => 9),
//                            array("type" => "wwww", "sort" => 7),
//                        )),
//                )),
//            array("type" => "fruit"),
//            array("type" => "milk", "sort" => 2.90),
//        );
//
//
//
//        $order = array();
//        foreach ($items as $key => $row) {
//
//            if (!isset($row['sort'])) {
//                $row['sort'] = 99999;
//            }
//
//            $order[$key] = $row['sort'];
//        }
//        array_multisort($order, SORT_ASC, $items);
//
//        $items = $this->sort($items);
//
//
//        return $items;

        if (Request::isMethod("POST")) {
            $items = json_decode(Request::get("items"));
            //$keys = $this->orders($items);
            return DB::table("options")->where("name", "sidebar")->update(array(
                "value" => json_encode($items)
            ));
            die();
        }


        //print_r(Menu::get("sidebar")->items());
        //die();
        return view("admin::sidebar", $this->data);
    }

}
