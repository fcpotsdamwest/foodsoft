<!DOCTYPE html>
<html lang="de">

<!-- For available variables consult the code where this template is loaded -->

<head>
    <meta charset="utf-8">
    <title>template</title>
</head>

<body>
    <p>
        {{ fc_name }}<br>
        Kundennr.: {{ fc_kundennummer }}<br>
    </p>
    <p>
        {{ lieferant_name }}<br>
        {{ lieferant_ort }}<br>
    </p>
    <div style="width:100%;text-align:right">{{ fc_ort }}, den {{ datum_heute }}</div>
    <p>
        {{ lieferant_anrede }}
    </p>
    <p>
        zur Lieferung am <b>{{ lieferdatum_trad }}</b>, bestellen wir hiermit:
    </p>
    <br>
    <p>
        {{ tabelle }}
    </p>
    <br>
    <p>
        {{ lieferant_grussformel }}
    </p>
    <p>
        {{ besteller_name }}
    </p>
</body>

</html>