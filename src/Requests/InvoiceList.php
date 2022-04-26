<?php

namespace Helori\LaravelSaas\Requests;

use Illuminate\Support\Arr;


class InvoiceList extends ActionRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->ownCurrentTeam();
    }
    
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
