<?php

namespace Helori\LaravelSaas\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Stripe\StripeClient;
use Helori\LaravelSaas\Models\Stripe\Price;
use Carbon\Carbon;
use Exception;


class UpdateStripeProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update-stripe-products {--clear}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update stripe products';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $clear = $this->option('clear', false);

        $stripe = new StripeClient(env('STRIPE_SECRET'));
        $products = config('saas.products');
        
        if($clear)
        {
            $this->line("=> Suppression des produits :");

            foreach($products as $product)
            {
                $this->line("========== ".$product['product_id'].' ==========');

                foreach($product['prices'] as $price)
                {
                    $this->info("-> Suppression du tarif : ".$price['price_id']);
                    //$p = $stripe->plans->delete($price['price_id'], []);
                    try{
                        $p = $stripe->plans->delete($price['price_id'], []);
                    }
                    catch(Exception $e)
                    {
                        
                    }
                }
                try{
                    $this->info("-> Suppression du produit : ".$product['product_id']);
                    $stripe->products->delete($product['product_id'], []);
                }
                catch(Exception $e)
                {
                    $this->error("-> Echec de la suppression du produit : ".$e->getMessage());
                }
            }
        }

        $stripeProducts = $stripe->products->all([]);
        $stripeProductsIds = [];
        foreach($stripeProducts as $stripeProduct)
        {
            $stripeProductsIds[] = $stripeProduct->id;
        }

        $this->line("=> Création des produits");
        $this->line("=> Produits existants dans Stripe : ".implode(', ', $stripeProductsIds));

        foreach($products as $product)
        {
            $this->line("========== ".$product['product_id'].' ==========');

            if(!in_array($product['product_id'], $stripeProductsIds))
            {
                $this->info("-> Création du produit : ".$product['product_id']);

                $stripe->products->create([
                    'id' => $product['product_id'],
                    'name' => $product['name'],
                    'description' => $product['short_description'],
                    'tax_code' => $product['tax_code'],
                ]);

                foreach($product['prices'] as $price)
                {
                    $this->info("-> Création du tarif : ".$price['price_id']);

                    $priceData = Arr::only($price, [
                        'unit_amount',
                        'currency',
                        'tax_behavior',
                        
                        'billing_scheme',
                        'recurring',

                        'tiers',
                        'tiers_mode',
                    ]);

                    $priceData['product'] = $product['product_id'];
                    $priceData['unit_amount'] *= 100;
                    $priceData['nickname'] = $price['name'];

                    $p = $stripe->prices->create($priceData);

                    $item = new Price();
                    $item->fill([
                        'price_id' => $p->id,
                        'created' => Carbon::createFromTimestamp($p->created)->format('Y-m-d H:i:s'),
                        'product' => $p->product,
                        'nickname' => $p->nickname,

                        'currency' => $p->currency,
                        'unit_amount' => $p->unit_amount,
                        'tax_behavior' => $p->tax_behavior,

                        'billing_scheme' => $p->billing_scheme,
                        'interval' => $p->recurring->interval,
                        'interval_count' => $p->recurring->interval_count,
                        'usage_type' => $p->recurring->usage_type,
                    ]);
                    $item->save();
                }
            }
            else
            {
                $this->warn("-> Le produit existe déjà : ".$product['product_id']);
            }
        }
        return Command::SUCCESS;
    }
}
