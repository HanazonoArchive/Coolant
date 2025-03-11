document.addEventListener("DOMContentLoaded", () => {
    console.log("Table script loaded");
    document.getElementById("generateQoutation").addEventListener("click", async function () {
        console.log("Generating quotation...");
        sendData();
    });
});

function addRow() {
    const tableBody = document.querySelector("#quotationTable tbody");
    const newRow = tableBody.insertRow();

    newRow.innerHTML = `
        <td><input class="titleContent_InputField" type="text" name="item[]" placeholder="Item"></td>
        <td><input class="titleContent_InputField" type="text" name="description[]" placeholder="Description"></td>
        <td><input class="titleContent_InputField" type="number" name="quantity[]" oninput="calculateTotal(this)"></td>
        <td><input class="titleContent_InputField" type="number" name="price[]" oninput="calculateTotal(this)"></td>
        <td><span>0.00</span></td>
        <td><button class="submitButton" onclick="deleteRow(this)">Delete</button></td>
    `;
}

function calculateTotal(input) {
    const row = input.closest("tr");
    const quantity = Number(row.cells[2].querySelector("input").value) || 0;
    const price = Number(row.cells[3].querySelector("input").value) || 0;
    const totalCell = row.cells[4].querySelector("span");

    totalCell.innerText = (quantity * price).toFixed(2);
    updateGrandTotal();
}

function updateGrandTotal() {
    const totalCells = document.querySelectorAll("#quotationTable tbody tr td:nth-child(5) span");
    const grandTotal = Array.from(totalCells).reduce((sum, cell) => sum + (Number(cell.innerText) || 0), 0);
    
    const grandTotalDisplay = document.getElementById("grandTotalInput");
    if (grandTotalDisplay) {
        grandTotalDisplay.innerText = grandTotal.toFixed(2);
    } else {
        console.error("Error: 'grandTotalInput' element not found.");
    }
}

function deleteRow(button) {
    button.closest("tr").remove();
    updateGrandTotal();
}

function sendData() {
    const tableRows = document.querySelectorAll("#quotationTable tbody tr");
    const items = [];
    let hasError = false;

    tableRows.forEach((row, index) => {
        let item = row.cells[0].querySelector("input").value.trim();
        let description = row.cells[1].querySelector("input").value.trim();        
        let quantity = row.cells[2].querySelector("input").value.trim();
        let price = row.cells[3].querySelector("input").value.trim();
        let total = row.cells[4].querySelector("span").innerText.trim();

        console.log(`Row ${index + 1}:`, { item, description, quantity, price, total });

        if (!item || !description || !quantity || !price) {
            hasError = true;
            console.error(`Row ${index + 1}: Missing required fields.`);
            return;
        }

        items.push({
            item,
            description,
            quantity: parseFloat(quantity),
            price: parseFloat(price),
            total: parseFloat(total),
        });
    });

    if (hasError) {
        alert("Please fill in all required fields before generating the quotation.");
        return;
    }

    const payload = {
        action: "quotationTABLE",
        items,
    };

    console.log("Sending data:", JSON.stringify(payload, null, 2));

    fetch("quotation.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(payload),
    })
    .then(response => response.text()) // Get raw response first
    .then(text => {
        console.log("Raw Response:", text); // Debugging step
        return JSON.parse(text.trim()); // Trim whitespace and parse
    })
    .then(data => {
        console.log("Parsed JSON:", data);
    })
    .catch(error => console.error("Fetch Error:", error));    
}
