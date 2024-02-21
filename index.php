<?php

// vunkce nacteni kurzu z CNB
function getExchangeRate($from, $to)
{
    $url = "https://api.exchangerate-api.com/v4/latest/" . $from;
    $data = json_decode(file_get_contents($url), true);
    return $data['rates'][$to];
}

$errors = [];
// validace formulare
if ($_SERVER["REQUEST_METHOD"] == "POST") {   
    $errors = [];
    if (empty($_POST["name"])) {
        $errors[] = "Jméno je povinné.";
    }
    if (empty($_POST["last_name"])) {
        $errors[] = "Příjmení je povinné.";
    }
    if (empty($_POST["email"]) || !filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
        $errors[] = "E-mail je povinný a musí být ve správném formátu.";
    }
    if (empty($_POST["product"])) {
        $errors[] = "Produkt je povinný.";
    }
    if (!is_numeric($_POST["price"]) || ($_POST["price"] <= 0)) {
        $errors[] = "Cena je povinný údaj a musí být kladné číslo.";
    }
    if (empty($_POST["quantity"]) || !is_numeric($_POST["quantity"])) {
        $errors[] = "Množství je povinný údaj.";
    }



    // kdyz nejsou chyby zobrazi se rekapitulace
    if (empty($errors)) {
        $name = $_POST["name"];
        $last_name = $_POST["last_name"];
        $email = $_POST["email"];
        $product = $_POST["product"];
        $price = $_POST["price"];
        $quantity = $_POST["quantity"];
        $currency = $_POST["currency"];

        $total_price = $price * $quantity;
        $vat = $total_price * 0.21;
        $total_with_vat = $total_price + $vat;

        // prepocty men dle CNB
        $exchange_rate = getExchangeRate($currency, "CZK");
        $total_price_currency = number_format($total_price / $exchange_rate, 2, '.', '');
        $total_with_vat_currency = number_format($total_with_vat / $exchange_rate, 2, '.', '');
    }
} ?>


<!DOCTYPE html>
<html lang="cs">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">

    <title>Formulář nákupu</title>


</head>


<body>

    <h1>Formulář nákupu</h1>

    <?php if (!empty($errors)) : ?>
        <div style="color: red;">
            <h2>Chyby při vyplňování formuláře:</h2>
            <ul>
                <?php foreach ($errors as $error) : ?>
                    <li><?php echo $error; ?></li>
                <?php endforeach; ?>
            </ul>
            <p>Prosím, doplňte chybející údaje</p>
        </div>
    <?php endif; ?>


    <section class="form">
        <section class="form-set">
            <form method="post" action="" class="box-form">
                <h2>Objednávka</h2><br>
                <input type="text" name="name" placeholder="Jméno">
                <input type="text" name="last_name" placeholder="Příjmení"><br>
                <input type="email" name="email" placeholder="E-mail">
                <input type="text" name="product" placeholder="Produkt:"><br>
                <input type="number" min="0" step="0.01" name="price" id="price" placeholder="Cena za kus (CZK):">
                <input type="number" min="1" name="quantity" id="quantity" placeholder="Množství:"><br>
                <select name="currency" id="currency" >
                    <option value="EUR">EUR</option>
                    <option value="USD">USD</option>
                    <option value="GBP">GBP</option>
                </select><br>
                <div id="totalPrice">Celkova cena: 0</div>
                <input type="submit" value="Odeslat">
            </form>
        </section>
        <?php if (empty($errors) && $_SERVER["REQUEST_METHOD"] == "POST") : ?>

            <section class="form-result">
                <h2>Rekapitulace</h2><br>
                <?php
                    echo "<p><strong>Jméno:</strong><br>{$name}</p>";
                    echo "<p><strong>Příjmení:</strong><br>{$last_name}</p>";
                    echo "<p><strong>E-mail:</strong><br>{$email}</p>";
                    echo "<p><strong>Produkt:</strong><br>{$product}</p>";
                    echo "<p><strong>Cena za kus:</strong><br>{$price} CZK</p>";
                    echo "<p><strong>Množství:</strong><br>{$quantity} kusů</p>";
                    echo "<p><strong>Celková cena bez DPH:</strong><br>{$total_price} CZK</p>";
                    echo "<p><strong>DPH (21%):</strong><br>{$vat} CZK</p>";
                    echo "<p><strong>Celková cena s DPH:</strong><br>{$total_with_vat} CZK</p>";
                    echo "<p><strong>Celková cena bez DPH ({$currency}):</strong><br>{$total_price_currency} {$currency}</p>";
                    echo "<p><strong>Celková cena s DPH ({$currency}):</strong><br>{$total_with_vat_currency} {$currency}</p>";
                ?>
            </section>

        <?php endif; ?>

    </section>
    <script src="script.js"></script>
</body>


</html>