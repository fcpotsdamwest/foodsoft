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
}
