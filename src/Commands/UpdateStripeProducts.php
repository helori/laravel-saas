<?php

namespace Helori\LaravelSaas\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Stripe\StripeClient;
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
                        'amount',
                        'billing_scheme',
                        'usage_type',
                        'aggregate_usage',
                        'tiers',
                        'tiers_mode',
                        'currency',
                        'interval',
                        'interval_count',
                    ]);
                    
                    $priceData['id'] = $price['price_id'];
                    $priceData['product'] = $product['product_id'];
                    $priceData['amount'] *= 100;
                    $priceData['nickname'] = $price['name'];

                    $p = $stripe->plans->create($priceData);

                    if(isset($price['tax_behavior'])){
                        $stripe->prices->update(
                            $price['price_id'],
                            [
                                'tax_behavior' => $price['tax_behavior'],
                            ]
                        );
                    }
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
