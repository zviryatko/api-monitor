<?php

declare(strict_types=1);

use Psr\Container\ContainerInterface;
use Mezzio\Application;
use Mezzio\MiddlewareFactory;

/**
 * Setup routes with a single request method:
 *
 * $app->get('/', App\Handler\HomePageHandler::class, 'home');
 * $app->post('/album', App\Handler\AlbumCreateHandler::class, 'album.create');
 * $app->put('/album/:id', App\Handler\AlbumUpdateHandler::class, 'album.put');
 * $app->patch('/album/:id', App\Handler\AlbumUpdateHandler::class, 'album.patch');
 * $app->delete('/album/:id', App\Handler\AlbumDeleteHandler::class, 'album.delete');
 *
 * Or with multiple request methods:
 *
 * $app->route('/contact', App\Handler\ContactHandler::class, ['GET', 'POST', ...], 'contact');
 *
 * Or handling all request methods:
 *
 * $app->route('/contact', App\Handler\ContactHandler::class)->setName('contact');
 *
 * or:
 *
 * $app->route(
 *     '/contact',
 *     App\Handler\ContactHandler::class,
 *     Mezzio\Router\Route::HTTP_METHOD_ANY,
 *     'contact'
 * );
 */
return function (Application $app, MiddlewareFactory $factory, ContainerInterface $container): void {
    $app->get('/', App\Handler\HomePageHandler::class, 'home');
    $app->get('/job', App\Handler\JobListHandler::class, 'job.list');
    $app->route('/job/add', App\Handler\JobFormHandler::class, ['GET', 'POST'], 'job.add');
    $app->route('/job/{id:\d}', App\Handler\JobFormHandler::class, ['GET', 'POST'], 'job.edit');
    $app->get('/p/{alias}', App\Handler\ProjectPageHandler::class, 'project');
    $app->get('/project', App\Handler\ProjectListHandler::class, 'project.list');
    $app->route('/project/add', App\Handler\ProjectFormHandler::class, ['GET', 'POST'], 'project.add');
    $app->route('/project/{id:\d}', App\Handler\ProjectFormHandler::class, ['GET', 'POST'], 'project.edit');
    $app->route('/user/login', App\Handler\LoginPageHandler::class, ['GET'], 'user.login');
    $app->route('/user/logout/{token}', App\Handler\LogoutPageHandler::class, ['GET'], 'user.logout');
};
