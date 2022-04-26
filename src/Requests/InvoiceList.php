<?php

namespace Helori\LaravelSaas\Requests;

use Illuminate\Support\Arr;


class InvoiceList extends ActionRequest
{
    /**
     * Run the action the request is supposed to execute
     *
     * @return void
     */
    public function action()
    {
        $billable = $this->user()->billable();

        $invoices = $billable->invoices();
        $upcoming = $billable->upcomingInvoice();

        return [
            'invoices' => $invoices,
            'upcoming' => $upcoming,
        ];
    }
}
