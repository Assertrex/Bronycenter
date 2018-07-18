<?php

$application->group('', function() {
    $this->group('/', function() {
        $this->get('', BronyCenter\Controller\IndexController::class . ':indexAction')
            ->setName('index');
    });

    $this->group('/auth', function() {
        $this->get('', BronyCenter\Controller\AuthController::class . ':indexAction')
            ->setName('authIndex');

        $this->get('/verify', BronyCenter\Controller\AuthController::class . ':verifyAction')
            ->setName('authVerify');

        $this->get('/resend', BronyCenter\Controller\AuthController::class . ':resendAction')
            ->setName('authResend');

        $this->post('/login', BronyCenter\Controller\AuthController::class . ':loginProcessAction')
            ->setName('authLogin');

        $this->post('/register', BronyCenter\Controller\AuthController::class . ':registerProcessAction')
            ->setName('authRegister');
    });

    $this->group('/social', function() {
        $this->get('', BronyCenter\Controller\FeedController::class . ':indexAction')
            ->setName('socialIndex');
    });
})->add(function ($request, $response, $next) {
    $request = $request->withAttribute('currentBase', getCurrentBasePath($request));
    $request = $request->withAttribute('currentGroups', getCurrentRouteGroups($request));

    $response = $next($request, $response);

    return $response;
});
