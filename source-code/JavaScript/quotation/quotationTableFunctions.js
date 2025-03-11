document.addEventListener("DOMContentLoaded", () => {
  console.log("quotationTableFunctions script loaded");

  const generateQuotationButton = document.getElementById("generateQoutation");
  if (generateQuotationButton) {
    generateQuotationButton.addEventListener("click", sendData);
  }

  document
    .querySelector("#quotationTable tbody")
    .addEventListener("input", (event) => {
      if (event.target.matches("sl-input")) {
        calculateTotal(event.target);
      }
    });

  document
    .querySelector("#quotationTable tbody")
    .addEventListener("click", (event) => {
      if (event.target.matches(".deleteRowButton")) {
        deleteRow(event.target);
      }
    });
});

function addRow() {
  const tableBody = document.querySelector("#quotationTable tbody");
  const newRow = tableBody.insertRow();
  newRow.innerHTML = `
        <td><sl-input size="small" class="titleContent_InputField" type="text" name="item[]" placeholder="Item"></sl-input></td>
        <td><sl-input size="small" class="titleContent_InputField" type="text" name="description[]" placeholder="Description"></sl-input></td>
        <td><sl-input size="small" class="titleContent_InputField" type="number" name="quantity[]" oninput="calculateTotal(this)"></sl-input></td>
        <td><sl-input size="small" class="titleContent_InputField" type="number" name="price[]" oninput="calculateTotal(this)"></sl-input></td>
        <td><sl-span class="totalCell">0.00</sl-span></td>
        <td><sl-button variant="primary" size="small" class="deleteRowButton">Delete</sl-button></td>
    `;
}

function calculateTotal(input) {
  const row = input.closest("tr");
  if (!row) return;

  const quantity =
    parseFloat(row.cells[2].querySelector("sl-input")?.value) || 0;
  const price = parseFloat(row.cells[3].querySelector("sl-input")?.value) || 0;
  const totalCell = row.cells[4].querySelector("sl-span");

  if (totalCell) {
    totalCell.innerText = (quantity * price).toFixed(2);
  }

  updateGrandTotal();
}

function updateGrandTotal() {
  const totalCells = document.querySelectorAll(
    "#quotationTable tbody .totalCell"
  );
  const grandTotal = [...totalCells].reduce(
    (sum, cell) => sum + (parseFloat(cell.innerText) || 0),
    0
  );

  const grandTotalDisplay = document.getElementById("grandTotalInput");
  if (grandTotalDisplay) {
    grandTotalDisplay.innerText = grandTotal.toFixed(2);
  }
}

function deleteRow(button) {
  const row = button.closest("tr");
  if (row) {
    row.remove();
    updateGrandTotal();
  }
}

async function sendData() {
  const tableRows = document.querySelectorAll("#quotationTable tbody tr");
  const items = [];

  for (const row of tableRows) {
    const item = row.cells[0].querySelector("sl-input")?.value?.trim();
    const description = row.cells[1].querySelector("sl-input")?.value?.trim();
    const quantity = parseFloat(row.cells[2].querySelector("sl-input")?.value);
    const price = parseFloat(row.cells[3].querySelector("sl-input")?.value);
    const total = parseFloat(row.cells[4].querySelector("sl-span")?.innerText);

    if (!item || !description || isNaN(quantity) || isNaN(price)) {
      alert(
        "Please fill in all required fields before generating the quotation."
      );
      return;
    }

    items.push({ item, description, quantity, price, total });
  }

  try {
    const response = await fetch(window.location.pathname, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ action: "quotationTABLE", items }),
    });

    const data = await response.json();
    console.log("Server Response:", data);
  } catch (error) {
    console.log("Error fetching data:", data);
    console.error("Fetch Error:", error);
  }
}
