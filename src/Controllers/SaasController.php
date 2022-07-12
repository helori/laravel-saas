<?php

namespace Helori\LaravelSaas\Controllers;

use Helori\LaravelSaas\Requests\ApiTokenCreate;
use Helori\LaravelSaas\Requests\ApiTokenRead;
use Helori\LaravelSaas\Requests\ApiTokenDelete;

use Helori\LaravelSaas\Requests\BrowserSessionRead;
use Helori\LaravelSaas\Requests\BrowserSessionDelete;

use Helori\LaravelSaas\Requests\TeamRead;
use Helori\LaravelSaas\Requests\TeamList;
use Helori\LaravelSaas\Requests\TeamUpdate;
use Helori\LaravelSaas\Requests\TeamSwitch;

use Helori\LaravelSaas\Requests\MemberList;
use Helori\LaravelSaas\Requests\MemberCreate;
use Helori\LaravelSaas\Requests\MemberUpdate;
use Helori\LaravelSaas\Requests\MemberDelete;
use Helori\LaravelSaas\Requests\MemberInvite;
use Helori\LaravelSaas\Requests\MemberLogin;

use Helori\LaravelSaas\Requests\CardIntent;
use Helori\LaravelSaas\Requests\CardRead;
use Helori\LaravelSaas\Requests\CardUpdate;
use Helori\LaravelSaas\Requests\CardDelete;

use Helori\LaravelSaas\Requests\ProductList;

use Helori\LaravelSaas\Requests\SubscriptionRead;
use Helori\LaravelSaas\Requests\SubscriptionCreate;
use Helori\LaravelSaas\Requests\SubscriptionDelete;

use Helori\LaravelSaas\Requests\InvoiceList;


class SaasController extends BaseController
{
    public function createApiToken(ApiTokenCreate $request) { return $request->action(); }
    public function readApiToken(ApiTokenRead $request) { return $request->action(); }
    public function deleteApiToken(ApiTokenDelete $request) { return $request->action(); }
    
    public function readBrowserSession(BrowserSessionRead $request) { return $request->action(); }
    public function deleteBrowserSession(BrowserSessionDelete $request) { return $request->action(); }

    public function readTeam(TeamRead $request) { return $request->action(); }
    public function listTeam(TeamList $request) { return $request->action(); }
    public function updateTeam(TeamUpdate $request) { return $request->action(); }
    public function switchTeam(TeamSwitch $request, $teamId) { return $request->action(); }

    public function memberList(MemberList $request, $teamId) { return $request->action(); }
    public function memberCreate(MemberCreate $request, $teamId) { return $request->action(); }
    public function memberUpdate(MemberUpdate $request, $teamId, $userId) { return $request->action(); }
    public function memberDelete(MemberDelete $request, $teamId, $userId) { return $request->action(); }
    public function memberInvite(MemberInvite $request, $teamId, $userId) { return $request->action(); }
    public function memberLogin(MemberLogin $request, $teamId, $userId) { return $request->action(); }

    public function cardIntent(CardIntent $request) { return $request->action(); }
    public function readCard(CardRead $request) { return $request->action(); }
    public function updateCard(CardUpdate $request) { return $request->action(); }
    public function deleteCard(CardDelete $request) { return $request->action(); }

    public function products(ProductList $request) { return $request->action(); }

    public function readSubscription(SubscriptionRead $request) { return $request->action(); }
    public function createSubscription(SubscriptionCreate $request) { return $request->action(); }
    public function deleteSubscription(SubscriptionDelete $request) { return $request->action(); }

    public function invoiceList(InvoiceList $request) { return $request->action(); }
}
