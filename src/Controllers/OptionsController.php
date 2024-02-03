<?php

namespace Dot\Platform\Controllers;

use Dot\Options\Facades\Option;
use Dot\Platform\Controller;
use Dot\Platform\Facades\Plugin;
use stdClass;

/*
 * Class OptionsController
 * @package Dot\Platform\Controllers
 */
class OptionsController extends Controller
{

    /*
     * Check for update
     * @return mixed
     */
    function check_update()
    {

        $current_version = Plugin::get("admin")->getVersion();


        $check = $this->get_latest_version();

        if(!$check) {
            return;
        }

        $last_version = $check->version;

        if (version_compare($last_version, $current_version, ">")) {
            Option::set("last_platform_version_check", $last_version);
            $this->data["version"] = $last_version;
        } else {
            $this->data["version"] = false;
        }

        return view()->make("admin::update", $this->data);
    }


    /*
     * Calling bitbucket API service
     * @return mixed
     */
    public function get_latest_version()
    {

        $objCurl = curl_init();

        curl_setopt($objCurl, CURLOPT_URL, "https://api.bitbucket.org/2.0/repositories/basemkhirat/dot-platform/refs/tags");
        curl_setopt($objCurl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($objCurl, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($objCurl);

        $versions = [];

        foreach (json_decode($response, true)["values"] as $tag) {
            $versions[strtotime($tag["date"])] = [
                "version" => $tag["name"],
                "message" => $tag["message"]
            ];
        }

        krsort($versions);

        $vers = [];

        foreach ($versions as $time => $data) {

            $ver = new stdClass();
            $ver->version = $data["version"];
            $ver->message = $data["message"];
            $ver->timestamp = $time;

            $vers[] = $ver;
        }

        return $vers[0];
    }
}
