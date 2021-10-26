<?php

namespace Helori\LaravelSaas\Requests;


class ProductList extends ActionRequest
{
    /**
     * Run the action the request is supposed to execute
     *
     * @return void
     */
    public function action()
    {
        return [
            'products' => config('saas.billables.team.products'),
            'plans' => config('saas.billables.team.plans'),
        ];
    }
}
