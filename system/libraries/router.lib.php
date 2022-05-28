<?php

/**
 * Router class for all controllers.
 */

class router
{
    /**
     * Constructor
     *
     * Parse the URL. Check the existence of the controller file. If not
     * found, used the default "welcome" controller. Check the existence of
     * the specified method. If it doesn't exist, use the default "index"
     * method. Ultimately, execute the controller->method.
     *
     */

    function __construct()
    {
        $controller = 'welcome';
        $method = 'index';

        $url = $this->get_url();
        if (empty($url)) {
            include CTLDIR . $controller . '.php';
            $controller = new $controller;
            $params = [];
            call_user_func_array([$controller, $method], $params);
        }
        else {

            if (file_exists(CTLDIR . $url[0] . '.php')) {
                $controller = $url[0];
                unset($url[0]);
            }

            include CTLDIR . $controller . '.php';
            $controller = new $controller;
            
            if (isset($url[1])) {
                if (method_exists($controller, $url[1])) {
                    $method = $url[1];
                    unset($url[1]);
                }
            }

            $params = $url ? array_values($url) : [];

            call_user_func_array([$controller, $method], $params);
        }

    }

    /**
     * Parse the GET parameter.
     *
     * URLs should be in the form index.php?url=controller/method/param1/param2
     *
     */

    function get_url()
    {
        if (isset($_GET['url'])) {
            $url = rtrim($_GET['url'], '/');
            $url = FILTER_VAR($url, FILTER_SANITIZE_URL);
            $parts = explode('/', $url);
            return $parts;
        }
        return [];
    }

}
