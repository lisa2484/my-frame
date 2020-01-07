<?php
function view($view, $push = array())
{
    if ($view != 'login') {
        foreach (array_keys($push) as $key) {
            $$key = $push[$key];
        }
        return include './views/' . $view . '.php';
    }
    else{
        return include './login.php';
    }
}
