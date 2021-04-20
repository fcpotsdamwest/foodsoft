<?php

use PHPUnit\Framework\TestCase;

final class ZuordnenZuteilungenBerechnenTest extends TestCase
{
    public function testZuteilungenBerechnenFirstComeFirstServe()
    {
        require '/src/code/zuordnen.php';

        /*
         * Who orders first, get assigned the product first
         *
         * TODO:
         * - howto mock
         * - howto parametrize
         *
         * Parameter structure ($mengen):
         * array(
                'produkt_name' => 'Pita Taschenbrot, Dinkel 4 St./280g',
                'produktgruppen_name' => 'Brot & Gebäck',
                'produktgruppen_id' => '10',
                'produkt_id' => '3858',
                'notiz' => '',
                'liefermenge' => '0.000',
                'gesamtbestellung_id' => '1138',
                'aufschlag_prozent' => '99.99',
                'liefereinheit' => '1 ST',
                'verteileinheit' => '1 ST',
                'lv_faktor' => 1.0,
                'gebindegroesse' => '12',
                'lieferpreis' => '1.6900',
                'preis_id' => '35649',
                'pfand' => '0.00',
                'mwst' => '5.00',
                'artikelnummer' => '13123980',
                'bestellnummer' => '13123980',
                'gesamtbestellmenge' => '6.000',
                'festbestellmenge' => '6.000',
                'basarbestellmenge' => '0.000',
                'toleranzbestellmenge' => '0.000',
                'verteilmenge' => '0.000',
                'muellmenge' => '0.000',
                'menge_ist_null' => '0',
                'nr' => 1,
                'kan_verteilmult' => 1.0,
                'kan_verteileinheit' => 'ST',
                'verteileinheit_anzeige' => '1 ST',
                'kan_verteileinheit_anzeige' => 'ST',
                'kan_verteilmult_anzeige' => 1.0,
                'kan_liefermult' => 1.0,
                'kan_liefereinheit' => 'ST',
                'liefereinheit_anzeige' => '1 ST',
                'kan_liefereinheit_anzeige' => 'ST',
                'kan_liefermult_anzeige' => 1.0,
                'nettolieferpreis' => '1.6900',
                'bruttolieferpreis' => 1.7745,
                'nettopreis' => 1.69,
                'bruttopreis' => 1.7745,
                'vpreis' => 1.7745,
                'lieferpreisaufschlag' => 1.6898309999999999,
                'preisaufschlag' => 1.6898309999999999,
                'endpreis' => 3.4643309999999996,
            )
         */

        $this->assertEquals(BESTELLZUORDNUNG_ART_VORMERKUNG_FEST, 10);
    }

    function zuteilungenBerechnenProvider()
    {
        $examples = [
            [
                'example_id' => 1,
                'product_id' => 1774,
                'festbestellungen' => [
                    0 => [
                        'produkt_id' => '1774',
                        'menge' => '1.000',
                        'art' => '20',
                        'zeitpunkt' => '2021-03-20 23:53:45',
                        'bestellzuordnung_id' => '843946',
                        'bestellgruppen_id' => '44',
                        'gesamtbestellung_id' => '1139',
                        'nr' => 1,
                    ],
                ],
                'result' => [
                    'bestellmenge' => 1,
                    'gebinde' => 1,
                    'festzuteilungen' => [ 44 => 1.0 ],
                    'toleranzzuteilungen' => [],
                ],
            ],
            [
                'example_id' => 2,
                'product_id' => 1747,
                'festbestellungen' => [
                    0 => [
                        'produkt_id' => '1747',
                        'menge' => '4.000',
                        'art' => '20',
                        'zeitpunkt' => '2021-02-15 22:30:19',
                        'bestellzuordnung_id' => '843897',
                        'bestellgruppen_id' => '45',
                        'gesamtbestellung_id' => '1139',
                        'nr' => 1,
                    ],
                    1 => [
                        'produkt_id' => '1747',
                        'menge' => '12.000',
                        'art' => '20',
                        'zeitpunkt' => '2021-02-15 22:30:42',
                        'bestellzuordnung_id' => '843898',
                        'bestellgruppen_id' => '2032',
                        'gesamtbestellung_id' => '1139',
                        'nr' => 2,
                    ],
                    2 => [
                        'produkt_id' => '1747',
                        'menge' => '4.000',
                        'art' => '20',
                        'zeitpunkt' => '2021-02-15 22:31:04',
                        'bestellzuordnung_id' => '843908',
                        'bestellgruppen_id' => '44',
                        'gesamtbestellung_id' => '1139',
                        'nr' => 3,
                    ],
                    3 => [
                        'produkt_id' => '1747',
                        'menge' => '2.000',
                        'art' => '20',
                        'zeitpunkt' => '2021-02-15 23:24:03',
                        'bestellzuordnung_id' => '843909',
                        'bestellgruppen_id' => '2032',
                        'gesamtbestellung_id' => '1139',
                        'nr' => 4,
                    ],
                    4 => [
                        'produkt_id' => '1747',
                        'menge' => '2.000',
                        'art' => '20',
                        'zeitpunkt' => '2021-02-15 23:24:08',
                        'bestellzuordnung_id' => '843919',
                        'bestellgruppen_id' => '2032',
                        'gesamtbestellung_id' => '1139',
                        'nr' => 5,
                    ],
                ],
                'result' => [
                    'bestellmenge' => 16,
                    'gebinde' => 1,
                    'festzuteilungen' => [
                        45 => 16,
                    ],
                    'toleranzzuteilungen' => [],
                ],
            ],
            [
                'example_id' => 3,
                'product_id' => 3863,
                'festbestellungen' => [
                    0 => [
                        'produkt_id' => '3863',
                        'menge' => '6.000',
                        'art' => '20',
                        'zeitpunkt' => '2021-02-27 23:18:07',
                        'bestellzuordnung_id' => '843935',
                        'bestellgruppen_id' => '44',
                        'gesamtbestellung_id' => '1139',
                        'nr' => 1,
                    ],
                    1 => [
                        'produkt_id' => '3863',
                        'menge' => '1.000',
                        'art' => '20',
                        'zeitpunkt' => '2021-02-27 23:18:54',
                        'bestellzuordnung_id' => '843936',
                        'bestellgruppen_id' => '2032',
                        'gesamtbestellung_id' => '1139',
                        'nr' => 2,
                    ],
                ],
                'result' => [
                    'bestellmenge' => 6,
                    'gebinde' => 1,
                    'festzuteilungen' => [
                        44 => 6,
                    ],
                    'toleranzzuteilungen' => [],
                ]
            ],
            [
                'example_id' => 4,
                'product_id' => 4188,
                'festbestellungen' => [
                    0 => [
                        'produkt_id' => '4188',
                        'menge' => '1.000',
                        'art' => '20',
                        'zeitpunkt' => '2021-02-22 22:38:29',
                        'bestellzuordnung_id' => '843929',
                        'bestellgruppen_id' => '44',
                        'gesamtbestellung_id' => '1139',
                        'nr' => 1,
                    ],
                    1 => [
                        'produkt_id' => '4188',
                        'menge' => '1.000',
                        'art' => '20',
                        'zeitpunkt' => '2021-02-22 22:38:34',
                        'bestellzuordnung_id' => '843930',
                        'bestellgruppen_id' => '44',
                        'gesamtbestellung_id' => '1139',
                        'nr' => 2,
                    ],
                    2 => [
                        'produkt_id' => '4188',
                        'menge' => '1.000',
                        'art' => '20',
                        'zeitpunkt' => '2021-02-22 22:38:40',
                        'bestellzuordnung_id' => '843931',
                        'bestellgruppen_id' => '44',
                        'gesamtbestellung_id' => '1139',
                        'nr' => 3,
                    ],
                    3 => [
                        'produkt_id' => '4188',
                        'menge' => '2.000',
                        'art' => '20',
                        'zeitpunkt' => '2021-02-22 22:38:49',
                        'bestellzuordnung_id' => '843932',
                        'bestellgruppen_id' => '44',
                        'gesamtbestellung_id' => '1139',
                        'nr' => 4,
                    ],
                    4 => [
                        'produkt_id' => '4188',
                        'menge' => '1.000',
                        'art' => '20',
                        'zeitpunkt' => '2021-02-22 22:38:57',
                        'bestellzuordnung_id' => '843933',
                        'bestellgruppen_id' => '44',
                        'gesamtbestellung_id' => '1139',
                        'nr' => 5,
                    ],
                    5 => [
                        'produkt_id' => '4188',
                        'menge' => '1.000',
                        'art' => '20',
                        'zeitpunkt' => '2021-02-22 22:39:29',
                        'bestellzuordnung_id' => '843934',
                        'bestellgruppen_id' => '45',
                        'gesamtbestellung_id' => '1139',
                        'nr' => 6,
                    ],    
                ],
                'result' => [
                    'bestellmenge' => 6,
                    'gebinde' => 1,
                    'festzuteilungen' => [ 44 => 6 ],
                    'toleranzzuteilungen' => [],
                ],
            ],
            [
                'example_id' => 5,
                'description' => 'One group orders a full bunch, and another one later claims a small amount',
                'product_id' => 3865,
                'festbestellungen' => [
                    0 => 
                    [
                        'produkt_id' => '3865',
                        'menge' => '6.000',
                        'art' => '20',
                        'zeitpunkt' => '2021-02-15 00:43:40',
                        'bestellzuordnung_id' => '843743',
                        'bestellgruppen_id' => '2032',
                        'gesamtbestellung_id' => '1139',
                        'nr' => 1,
                    ],
                    1 => [
                        'produkt_id' => '3865',
                        'menge' => '1.000',
                        'art' => '20',
                        'zeitpunkt' => '2021-02-15 01:24:36',
                        'bestellzuordnung_id' => '843757',
                        'bestellgruppen_id' => '44',
                        'gesamtbestellung_id' => '1139',
                        'nr' => 2,
                    ],
                ],
                'result' => [
                    'bestellmenge' => 6,
                    'gebinde' => 1,
                    'festzuteilungen' => [ 2032 => 6 ],
                    'toleranzzuteilungen' => [],
                ],
            ],
        ];

        $testcases = null;

        return $testcases;
    }
}
