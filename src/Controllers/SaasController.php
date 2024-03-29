<?php

namespace Helori\LaravelSaas\Controllers;

use Helori\LaravelSaas\Requests\ApiTokenCreate;
use Helori\LaravelSaas\Requests\ApiTokenRead;
use Helori\LaravelSaas\Requests\ApiTokenDelete;

use Helori\LaravelSaas\Requests\BrowserSessionRead;
use Helori\LaravelSaas\Requests\BrowserSessionDelete;

use Helori\LaravelSaas\Requests\TeamCreate;
use Helori\LaravelSaas\Requests\TeamRead;
use Helori\LaravelSaas\Requests\TeamList;
use Helori\LaravelSaas\Requests\TeamUpdate;
use Helori\LaravelSaas\Requests\TeamSwitch;

use Helori\LaravelSaas\Requests\UserDelete;

use Helori\LaravelSaas\Requests\MemberList;
use Helori\LaravelSaas\Requests\MemberCreate;
use Helori\LaravelSaas\Requests\MemberUpdate;
use Helori\LaravelSaas\Requests\MemberDelete;
use Helori\LaravelSaas\Requests\MemberInvite;
use Helori\LaravelSaas\Requests\MemberLogin;

use Helori\LaravelSaas\Requests\PaymentMethodIntent;
use Helori\LaravelSaas\Requests\PaymentMethodRead;
use Helori\LaravelSaas\Requests\PaymentMethodUpdate;
use Helori\LaravelSaas\Requests\PaymentMethodDelete;

use Helori\LaravelSaas\Requests\ProductList;
use Helori\LaravelSaas\Requests\PriceList;

use Helori\LaravelSaas\Requests\SubscriptionRead;
use Helori\LaravelSaas\Requests\SubscriptionCreate;
use Helori\LaravelSaas\Requests\SubscriptionDelete;

use Helori\LaravelSaas\Requests\InvoiceList;

use Helori\LaravelSaas\Requests\PromotionRead;
use Helori\LaravelSaas\Requests\PromotionApply;


class SaasController extends BaseController
{
    public function createApiToken(ApiTokenCreate $request) { return $request->action(); }
    public function readApiToken(ApiTokenRead $request) { return $request->action(); }
    public function deleteApiToken(ApiTokenDelete $request) { return $request->action(); }
    
    public function readBrowserSession(BrowserSessionRead $request) { return $request->action(); }
    public function deleteBrowserSession(BrowserSessionDelete $request) { return $request->action(); }

    public function createTeam(TeamCreate $request) { return $request->action(); }
    public function readTeam(TeamRead $request) { return $request->action(); }
    public function listTeam(TeamList $request) { return $request->action(); }
    public function updateTeam(TeamUpdate $request) { return $request->action(); }
    public function switchTeam(TeamSwitch $request, $teamId) { return $request->action(); }

    public function userDelete(UserDelete $request) { return $request->action(); }

    public function memberList(MemberList $request, $teamId) { return $request->action(); }
    public function memberCreate(MemberCreate $request, $teamId) { return $request->action(); }
    public function memberUpdate(MemberUpdate $request, $teamId, $userId) { return $request->action(); }
    public function memberDelete(MemberDelete $request, $teamId, $userId) { return $request->action(); }
    public function memberInvite(MemberInvite $request, $teamId, $userId) { return $request->action(); }
    public function memberLogin(MemberLogin $request, $teamId, $userId) { return $request->action(); }

    public function paymentMethodIntent(PaymentMethodIntent $request) { return $request->action(); }
    public function paymentMethodRead(PaymentMethodRead $request) { return $request->action(); }
    public function paymentMethodUpdate(PaymentMethodUpdate $request) { return $request->action(); }
    public function paymentMethodDelete(PaymentMethodDelete $request) { return $request->action(); }

    public function products(ProductList $request) { return $request->action(); }
    public function prices(PriceList $request) { return $request->action(); }

    public function readSubscription(SubscriptionRead $request) { return $request->action(); }
    public function createSubscription(SubscriptionCreate $request) { return $request->action(); }
    public function deleteSubscription(SubscriptionDelete $request) { return $request->action(); }

    public function invoiceList(InvoiceList $request) { return $request->action(); }

    public function readPromotion(PromotionRead $request) { return $request->action(); }
    public function applyPromotion(PromotionApply $request) { return $request->action(); }
}
