<?php

return [

    'billables' => [
        'team' => [
            'model' => App\Models\User\Team::class,
            // Les différents prix possibles pour un plan (monthly / yearly, ...)
            'plans' => [
                [
                    'name' => 'Région',
                    'slug' => 'region',
                ],
                [
                    'name' => 'Circonscription',
                    'slug' => 'circonscription',
                ],
            ],
            // Les différents produits vendus dans l'application,
            // chacun ayant ses propres plans (Standard, Premium...), 
            // qui peuvent eux-mêmes avoir leurs propres prix (mensuel, annuel, ...)
            'products' => [
                [
                    'name' => 'Procurations',
                    'slug' => 'procuration',
                    'product_id' => 'prod_KTL4EXOM58JSKH',
                    'short_description' => 'Mise en relation de mandant et de mandataires',
                    'trial_days' => 10,
                    'features' => [
                        'Utilisateurs illimités',
                        'Mandants et mandataires illimités',
                        'Matching géographique par adresse',
                        'Génération des formulaires de procuration',
                        'Emails personnalisables',
                        "Connexion à votre CRM (API + Zapier) + Exports Excel",
                    ],
                    'plans' => [
                        [
                            'name' => 'Région',
                            'slug' => 'region',
                            'prices' => [
                                [
                                    'name' => 'Tarif mensuel',
                                    'slug' => 'monthly',
                                    'price_id' => 'price_1JoOO7Ecr2fMUnbCxps7gUa6',
                                    'price' => 5000,
                                    'unit' => '€ / mois',
                                ],
                                [
                                    'name' => 'Tarif annuel',
                                    'slug' => 'yearly',
                                    'price_id' => 'price_1Jok7fEcr2fMUnbCzjTSxyEA',
                                    'price' => 60000,
                                    'unit' => '€ / an',
                                ],
                            ],
                        ],
                        [
                            'name' => 'Circonscription',
                            'slug' => 'circonscription',
                            'prices' => [
                                [
                                    'name' => 'Tarif mensuel',
                                    'slug' => 'circonscription',
                                    'price_id' => 'price_1JoOO7Ecr2fMUnbCGaeFaxKE',
                                    'price' => 1000,
                                    'unit' => '€ / mois',
                                ],
                                [
                                    'name' => 'Tarif annuel',
                                    'slug' => 'circonscription',
                                    'price_id' => 'price_1Jok8LEcr2fMUnbCrhgzbhgy',
                                    'price' => 12000,
                                    'unit' => '€ / an',
                                ],
                            ],
                        ],
                    ],
                ],
                [
                    'name' => 'Inscriptions',
                    'slug' => 'inscription',
                    'product_id' => 'prod_KTL74CLCK1mjKI',
                    'short_description' => "Incitation à s'inscrire sur les listes électorales",
                    'trial_days' => 0,
                    'features' => [
                        "Utilisateurs illimités",
                        "Demandes illimités",
                        "Importation des listes électorales",
                        "Optimisations SEO",
                        "Emails d'accompagnement personnalisables",
                        "Connexion à votre CRM (API + Zapier) + Exports Excel",
                    ],
                    'plans' => [
                        [
                            'name' => 'Région',
                            'slug' => 'region',
                            'prices' => [
                                [
                                    'name' => 'Tarif mensuel',
                                    'slug' => 'monthly',
                                    'price_id' => 'price_1JoOQmEcr2fMUnbCUa4c4ZZq',
                                    'price' => 2000,
                                    'unit' => '€ / mois',
                                ],
                            ],
                        ],
                        [
                            'name' => 'Circonscription',
                            'slug' => 'circonscription',
                            'prices' => [
                                [
                                    'name' => 'Tarif mensuel',
                                    'slug' => 'monthly',
                                    'price_id' => 'price_1JoOQmEcr2fMUnbCBGyUc3Mp',
                                    'price' => 500,
                                    'unit' => '€ / mois',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
];
