/*
 * Mit diesem Skript kann man alle aktuellen Preiseinträge zu einem definierten
 * Termin auf neue MwSt-Sätze umstellen.
 *
 * Für Bestellungen, deren Liefertermin nach dem Termin liegt, werden diese
 * neuen Preiseinträge gesetzt, sofern sie noch auf Preiseinträge mit altem
 * MwSt-Satz verweisen.
 *
 * Verwendung:
 *
 * 1. Backup der Datenbank machen:
 * # mysqldump <DATABASENAME> | bzip2 > backup.sql.bz2
 *
 * 2. Skript konfigurieren (falls nötig):
 * # vi mwst.sql
 *
 * 3. Skript ausführen:
 * # mysql <DATABASENAME> < mwst.sql
 *
 * 4. Fertig :-D
 *
 * Bemerkungen:
 * Dieses Skript ist (hoffentlich) idempotent, kann also mehrfach ausgeführt werden und hat nur beim
 * ersten Mal einen Effekt.
 *
 * Sollte einem eine Bestellung durch die Lappen gegangen sein, weil das Lieferdatum vor dem
 * Stichtag lag, kann man das Lieferdatum ändern und das Skript nochmal laufen lassen, um diese
 * Bestellung nachträglich zu konvertieren.
 */

/*
 * Skript-Konfiguration:
 */
set @vat_default_old = 16, @vat_default_new = 19;
set @vat_reduced_old = 5, @vat_reduced_new = 7;
set @changetime = timestamp('2021-01-01 00:00:00');
/*
 * Umstellung der Pfand-Rückgaben kann im allgemeinen drei Monate später erfolgen, siehe
 * https://datenbank.nwb.de/Dokument/Anzeigen/827824/
 */
set @changetime_deposit_return = timestamp('2020-10-01 00:00:00');
/* Die meisten Händler machen davon keinen Gebrauch und stellen zum gleichen Zeitpunkt um: */
set @changetime_deposit_return = @changetime;

/*
 * Ausführung:
 */
select concat('Setze Katalog-MwSt-Sätze auf ', @vat_reduced_new, '% und ', @vat_default_new, '%:') as '';
update leitvariable set value=@vat_reduced_new where name='katalog_mwst_reduziert';
update leitvariable set value=@vat_default_new where name='katalog_mwst_standard';
select name, value from leitvariable where name like 'katalog_mwst_%';

select concat('Suche aktuelle Preiseinträge mit MwSt ', @vat_reduced_old, '% oder ', @vat_default_old, '%:') as '';

drop table if exists new_produktpreise;
create temporary table new_produktpreise as select * from produktpreise
    where mwst in (@vat_reduced_old, @vat_default_old)
    and zeitende is null;
select concat(row_count(), ' gefunden.') as '';
alter table new_produktpreise modify id int(11) null;

drop table if exists updated_produktpreise;
create temporary table updated_produktpreise as select * from new_produktpreise;
alter table updated_produktpreise modify id int(11) null;

select 'Schließe aktuelle Preiseinträge ab.' as '';
update updated_produktpreise set zeitende=timestampadd(SECOND, -1, @changetime);

select 'Erzeuge neue Preiseinträge.' as '';
update new_produktpreise set
    id=NULL,
    zeitstart=@changetime,
    mwst=
    case
        when mwst = @vat_reduced_old then @vat_reduced_new
        when mwst = @vat_default_old then @vat_default_new
        else mwst
    end;

select 'Pflege Änderungen ein.' as '';
update produktpreise as d, updated_produktpreise as s set d.zeitende = s.zeitende where d.id = s.id;
insert into produktpreise select * from new_produktpreise;

select 'Aktualisiere Preiseinträge in aktuellen Bestellungen' as '';
update bestellvorschlaege as d,
    /* erste Referenz um den korrekten neuen Eintrag zu selektieren */
    produktpreise as s,
    /* zweite Referenz, um zu überprüfen dass der alte Eintrag noch falsch ist */
    produktpreise as c,
    gesamtbestellungen as g
    set d.produktpreise_id = s.id
    where s.produkt_id = d.produkt_id
        and s.zeitende is null
        and c.id = d.produktpreise_id
        and c.mwst in (@vat_reduced_old, @vat_default_old)
        and d.gesamtbestellung_id = g.id
        and g.lieferung >= @changetime;

select concat(row_count(), ' Einträge aktualisiert.') as '';

select 'Fertig :-)' as '';
