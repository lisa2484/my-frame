<?php
function view($view, $push = array())
{
    if ($view != 'login') {
        foreach (array_keys($push) as $key) {
            $$key = $push[$key];
        }
        include './views/' . $view . '.php';
    } else {
        include './login.php';
    }
}
