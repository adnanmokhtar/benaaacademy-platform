<?php

namespace Dot\Platform\Classes;

/*
 * Class DotResponse
 * Create the API Response output
 */
class Response
{

    /*
     * @var string
     */
    public $responseBody = "response";
    /*
     * @var string
     */
    public $errorBody = "error";
    /*
     * @var string
     */
    public $messageBody = "message";
    /*
     * @var string
     */
    public $statusBody = "status";
    /*
     * @var string
     */
    public $codeBody = "code";
    /*
     * @var Response
     */
    private $response;

    /*
     * DotResponse constructor.
     */
    function __construct()
    {
        $this->response = response();
    }

    /*
     * @return mixed
     */
    public function getContent()
    {
        return $this->{$this->responseBody};
    }

    /*
     * @return mixed
     */
    public function getError()
    {
        return $this->{$this->errorBody};
    }

    /*
     * @return mixed
     */
    public function getCode()
    {
        return $this->{$this->codeBody};
    }

    /*
     * @param $data
     * @param $error
     * @param int $code
     * @return \Illuminate\Http\JsonResponse
     */
    public function json($data, $error = null, $code = 200)
    {

        $status = false;
        if (!$error) {
            $status = true;
        }

        return $this->response->json([
            $this->statusBody => $status,
            $this->responseBody => $data,
            $this->errorBody => $error,
            $this->codeBody => $code

        ]);

    }


}
