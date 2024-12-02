<?php

if (!function_exists('isActive')) {
    function isActive($routeNames, $activePage)
    {
        return in_array($activePage, (array)$routeNames) ? 'active' : 'text-dark';
    }
}

if (!function_exists('isShow')) {
    function isShow($routeNames, $activePage)
    {
        return in_array($activePage, (array)$routeNames) ? 'show' : '';
    }
}
