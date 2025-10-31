<?php

declare(strict_types=1);

use Slim\Routing\RouteCollectorProxy;

use App\Controller\{
    MasterData
};
use App\Controller\Staff\{
    Letter,
    GuestBook,
    MasterData as MasterDataStaff,
};
use App\Controller\{
    Guest
};

/* ------------------------------ General Routes ------------------------------ */

// Start Route
$app->get('/', 'App\Controller\Hello:getStatusAPI')->setName('main');

// Authentication Routes
$app->post('/v1/auth/signin', 'App\Controller\Auth:signin')->setName('signin');

// Users Routes
$app->group('/v1/user', function (RouteCollectorProxy $group) {
    $group->get('/info', 'App\Controller\User:info');
    $group->get('/profile', 'App\Controller\User:profile');
    $group->post('/profile/save', 'App\Controller\User:save');
    $group->post('/profile/change_password', 'App\Controller\User:changePassword');
});

function routes($app, $url, $controller): void {
    $app->group("/v1/$url", function (RouteCollectorProxy $group) use($controller) {
        $group->get('', [$controller, 'index']);
        $group->post('/save', [$controller, 'save']);
        $group->get('/{id}', [$controller, 'detail']);
        $group->delete('/bulk-delete', [$controller, 'bulkDelete']);
        $group->delete('[/{id}]', [$controller, 'delete']);
    });
}

// Diagnostic Data
$app->get('/diagnostic', 'App\Controller\Hello:getDiagnostic')->setName('diagnostic');

// Test Connect Database
$app->get('/testgetdata', 'App\Controller\Hello:testConnectFetchData');

$app->group('/v1/master-data', function (RouteCollectorProxy $group) {
    $group->get('/list-organization', [MasterData::class, 'listOrganization']);
    $group->get('/list-purpose', [MasterData::class, 'listPurpose']);
    $group->get('/event/detail/{id', [MasterData::class, 'listPurpose']);
});

$app->group('/v1/staff/master-data', function (RouteCollectorProxy $group) {
    $group->get('/list-staff', [MasterDataStaff::class, 'listStaff']);
    $group->get('/list-teacher', [MasterDataStaff::class, 'listTeacher']);
});
/* --------------------------------------------------------------------------- */

/* ------------------------------ Staff Routes ------------------------------ */
$app->get('/v1/staff/dashboard', [DashboardStudent::class, 'index']);
$app->get('/v1/staff/dashboard/log-activity', [DashboardStudent::class, 'activityLog']);

$app->post('/v1/staff/letter/incoming/assign', [Letter::class, 'assign']);
routes($app, 'staff/letter/incoming', Letter::class);

$app->delete('/v1/staff/guestbook/event/member/[{id}]', [GuestBook::class, 'deleteEventGuest']);
$app->get('/v1/staff/guestbook/event/member', [GuestBook::class, 'listEventMember']);

$app->get('/v1/staff/guestbook/event', [GuestBook::class, 'listEvent']);
$app->get('/v1/staff/guestbook/event/{id}', [GuestBook::class, 'detailEvent']);
$app->post('/v1/staff/guestbook/event/save', [GuestBook::class, 'saveEvent']);
$app->delete('/v1/staff/guestbook/event/[{id}]', [GuestBook::class, 'deleteEvent']);

routes($app, 'staff/guestbook', GuestBook::class);

$app->post('/v1/guestbook/guest/register', [Guest::class, 'register']);
$app->post('/v1/guestbook/guest/register-event', [Guest::class, 'registerEvent']);
$app->get('/v1/guestbook/guest/event/{id}', [Guest::class, 'detailEvent']);

/* ------------------------------ Super Admin Routes ------------------------------ */
$app->get('/v1/admin/dashboard', [DashboardAdmin::class, 'index']);
$app->get('/v1/admin/dashboard/log-activity', [DashboardAdmin::class, 'activityLog']);

// routes($app, 'program', Program::class);