<?php

namespace Benaaacademy\Platform;

use BenaaacademyResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as LaravelController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
 * Class ApiController
 * @package Benaaacademy\Platform
 */
class ApiController extends LaravelController
{

    /*
     * @var Request
     */
    public $request;

    /*
     * @var \BenaaacademyResponse
     */
    public $response;

    /*
     * The current user
     * @var
     */
    public $user;

    /*
     * ApiController constructor.
     */
    function __construct(Request $request)
    {

        $this->request = $request;
        $this->response = new BenaaacademyResponse();

        if (Auth::guard("api")->check()) {
            $this->user = Auth::guard(GUARD)->user();
        }
    }

    /*
     * @param null $error
     * @return \Illuminate\Http\JsonResponse
     */
    function error($error = NULL, $code = 500)
    {
        return $this->response([], $error, $code);
    }

    /*
     * @param array $data
     * @param null $error
     * @param int $code
     * @return \Illuminate\Http\JsonResponse
     */
    function response($data = [], $error = NULL, $code = 200)
    {
        return $this->response->json($data, $error, $code);
    }

    /*
     * Call internal API using GET
     * @param null $path
     * @param array $params
     * @return mixed
     */
    function get($path, $params = [])
    {
        return $this->call($path, "GET", $params);
    }

    /*
     * Call internal API
     * @param $path
     * @param string $method
     * @param array $params
     * @return mixed
     */
    function call($path, $method = "GET", $params = [])
    {

        $request = Request::create(API . '/' . $path, $method, $params);

        $originalInput = $this->request->input();

        $this->request->replace($request->input());

        $response = Route::dispatch($request);

        $this->request->replace($originalInput);

        return json_decode($response->content());

    }

    /*
     * Call internal API using POST
     * @param null $path
     * @param array $params
     * @return mixed
     */
    function post($path, $params = [])
    {
        return $this->call($path, "POST", $params);
    }

}
