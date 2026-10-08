document.addEventListener("DOMContentLoaded", function () {

    const quantity = document.getElementById("quantity");
    const paperTypes = document.querySelectorAll(
        'input[name="paper_type"]'
    );

    const priceElement = document.getElementById("price");

    const productId = document.querySelector(
        'input[name="product_id"]'
    ).value;


    function getSelectedPaper() {

        const selected = document.querySelector(
            'input[name="paper_type"]:checked'
        );

        return selected ? selected.value : "";
    }


    function calculatePrice() {

        const selectedQuantity = quantity.value;
        const selectedPaper = getSelectedPaper();

        if (!selectedQuantity || !selectedPaper) {
            return;
        }

        const formData = new FormData();

        formData.append(
            "product_id",
            productId
        );

        formData.append(
            "quantity",
            selectedQuantity
        );

        formData.append(
            "paper_type",
            selectedPaper
        );


        priceElement.textContent = "Loading...";


        fetch("ajax/calculate-price.php", {

            method: "POST",

            body: formData

        })

        .then(response => response.json())

        .then(data => {

            if (data.success) {

                priceElement.textContent = data.price;

            } else {

                priceElement.textContent = "N/A";

                alert(data.message);

            }

        })

        .catch(error => {

            console.error(error);

            priceElement.textContent = "Error";

        });

    }


    quantity.addEventListener(
        "change",
        calculatePrice
    );


    paperTypes.forEach(function (paper) {

        paper.addEventListener(
            "change",
            calculatePrice
        );

    });


    calculatePrice();

});