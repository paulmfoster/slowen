<?php

// This class is designed to be extended.

class controller
{
    function model($model, $params = NULL)
    {
        include MODELDIR . $model . '.php';
        if (is_null($params)) {
            return new $model();
        }
        else {
            return new $model($params);
        }
    }

    function view($view_file, $data = NULL)
    {
        if (file_exists(VIEWDIR . $view_file)) {
            include VIEWDIR . $view_file;
        }
        else {
            die('View file ' . VIEWDIR . $view_file . ' does not exist');
        }
    }

}
