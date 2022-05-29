<?php

/**
 * Master controller class.
 */

class controller
{
    /**
     * Model
     *
     * Instantiate the model and return the object.
     *
     * @param string The model
     * @param mixed Parameters
     * @return object The constructed class object
     */

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

    /**
     * View
     *
     * Include the view file with any parameters
     *
     * @param string View file
     * @param array Any data to pass
     */

    function view($view_file, $data = NULL)
    {
        if (file_exists(VIEWDIR . $view_file)) {
            include VIEWDIR . $view_file;
        }
        else {
            die('View file ' . VIEWDIR . $view_file . ' does not exist');
        }
		exit();
    }

}
