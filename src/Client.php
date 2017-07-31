<?php
/**
 * Created by PhpStorm.
 * User: michel
 * Date: 7/27/17
 * Time: 10:40 AM
 */

namespace Mr\Sdk;


class Client extends \GuzzleHttp\Client
{
    public function getArray()
    {
        $response = call_user_func_array([$this, 'get'], func_get_args());

        return json_decode($response->getBody(), true);
    }
}