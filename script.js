function calculateTotal() {
    var price = document.getElementById('price').value;
    var quantity = document.getElementById('quantity').value;
    var currency = document.getElementById('currency').value;
    var totalElement = document.getElementById('totalPrice');

    var myHeaders = new Headers();
    myHeaders.append("apikey", "fMQSw4644dwPEZDW3fyPjXx7lvpAbu0X");

    var requestOptions = {
        method: 'GET',
        redirect: 'follow',
        headers: myHeaders
    };

    // aktualni kurz pomoci api
    fetch("https://api.apilayer.com/exchangerates_data/convert?to=" + currency + "&from=CZK&amount=" + price, requestOptions)
        .then(response => response.json())
        .then(result => {
            var rate = result.info.rate;
            var total = (price * quantity * rate).toFixed(2);
            totalElement.innerText = 'Celková cena (' + currency + '): ' + total;
        })
        .catch(error => {
            console.error('Chyba při načítání kurzu: ', error);
            totalElement.innerText = 'Zvolte měnu';
        });
}

document.getElementById('price').addEventListener('input', calculateTotal);
document.getElementById('quantity').addEventListener('input', calculateTotal);
document.getElementById('currency').addEventListener('change', calculateTotal);

// init pri nacteni stranky
document.addEventListener('DOMContentLoaded', calculateTotal);
