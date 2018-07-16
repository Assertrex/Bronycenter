<?php

function getCurrentBasePath($request) : string
{
    $basePath = $request->getUri()->getBasePath();

    if (!empty($basePath)) {
        $basePath .= '/';
    }

    return $basePath;
}

function getCurrentRouteGroups($request) : array
{
    $currentGroup = [];
    $routeGroups = $request->getAttribute('route')->getGroups();

    foreach ($routeGroups as $routeGroup) {
        $currentGroup[] = $routeGroup->getPattern();
    }

    return $currentGroup;
}
