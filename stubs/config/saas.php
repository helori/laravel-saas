<?php

return [

    'user_model' => App\Models\User::class,
    'team_model' => App\Models\Team::class,

    // Les différents produits vendus dans l'application,
    // chacun ayant ses propres plans (Standard, Premium...), 
    // qui peuvent eux-mêmes avoir leurs propres prix (mensuel, annuel, ...)
    'products' => [
        [
            'name' => 'Product name',
            'slug' => 'Product slug',
            'product_id' => 'prod_XXXXXXXXX', // stripe product identifier
            'short_description' => "Product description",
            'trial_days' => 10,
            'features' => [
                "Feature 1",
                "Feature 2",
            ],
            'plans' => [
                [
                    'name' => 'Premium',
                    'slug' => 'premium',
                    'prices' => [
                        [
                            'name' => 'Monthly',
                            'slug' => 'monthly',
                            'price_id' => 'price_XXXXXXXX',
                            'price' => 100,
                            'unit' => '€ / mois',
                        ],
                    ],
                ],
            ],
        ],
    ],
];
