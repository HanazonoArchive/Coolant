function updateDetails(data) {
    console.log("Schedule Details JS Loaded");

    document.getElementById('customer_id').textContent = data.Customer_ID;
    document.getElementById('customer_name').textContent = data.Customer_Name;
    document.getElementById('customer_contact-number').textContent = data.Contact_Number;
    document.getElementById('customer_address').textContent = data.Address;
    document.getElementById('appointment_id').textContent = data.Ticket_Number;
    document.getElementById('appointment_date').textContent = data.Appointment_Date;
    document.getElementById('appointment_category').textContent = data.Category;

    // STATUS ELEMENT
    const statusElement = document.getElementById('appointment_status');
    statusElement.textContent = data.Status;

    let statusColor = "#000"; // Default Black
    let statusBgColor = "#1A1A1E"; // Default Light Gray
    switch (data.Status) {
        case "Pending":
            statusColor = "#EDB013";
            statusBgColor = "#1A1A1E";
            break;
        case "Working":
            statusColor = "#27BAFD";
            statusBgColor = "#1A1A1E";
            break;
        case "Completed":
            statusColor = "#3ADF76";
            statusBgColor = "#1A1A1E";
            break;
        case "Cancelled":
            statusColor = "#EC5758";
            statusBgColor = "#1A1A1E";
            break;
    }
    applyStyles(statusElement, statusColor, statusBgColor);

    // PRIORITY ELEMENT
    const priorityElement = document.getElementById('appointment_priority');
    priorityElement.textContent = data.Priority;

    let priorityColor = "#000"; // Default Black
    let priorityBgColor = "#f8f9fa"; // Default Light Gray
    switch (data.Priority) {
        case "Low":
            priorityColor = "#27BAFD";
            priorityBgColor = "#1A1A1E";
            break;
        case "Medium":
            priorityColor = "#EDB013";
            priorityBgColor = "#1A1A1E";
            break;
        case "High":
            priorityColor = "#3ADF76";
            priorityBgColor = "#1A1A1E";
            break;
        case "Urgent":
            priorityColor = "#EC5758";
            priorityBgColor = "#1A1A1E";
            break;
    }
    applyStyles(priorityElement, priorityColor, priorityBgColor);

    // APPOINTMENT DATE COLOR
    const dateElement = document.getElementById('appointment_date');
    let dateColor = getDateColor(data.Appointment_Date);
    let dateBgColor = getLightColor(dateColor);
    applyStyles(dateElement, dateColor, dateBgColor);

    // Debugging Logs
    console.log(`Status: ${data.Status}, Applied Color: ${statusColor}`);
    console.log(`Priority: ${data.Priority}, Applied Color: ${priorityColor}`);
    console.log(`Appointment Date: ${data.Appointment_Date}, Applied Color: ${dateColor}`);
}

// Function to determine the date color
function getDateColor(appointmentDate) {
    const currentDate = new Date();
    const appointment = new Date(appointmentDate);
    const timeDiff = appointment - currentDate;
    const daysDiff = timeDiff / (1000 * 60 * 60 * 24); // Convert ms to days

    if (daysDiff <= 7) {
        return "#EC5758"; // Red (Less than a week)
    } else if (appointment.getMonth() === currentDate.getMonth() && appointment.getFullYear() === currentDate.getFullYear()) {
        return "#3ADF76"; // Yellow (This month)
    } else {
        return "#27BAFD"; // Green (More than a month away)
    }
}

// Function to get light version of a color
function getLightColor(color) {
    const lightColors = {
        "#1A1A1E": "#1A1A1E", // Light Red
        "#1A1A1E": "#1A1A1E", // Light Yellow
        "#1A1A1E": "#1A1A1E", // Light Green
        "#1A1A1E": "#1A1A1E"  // Light Blue
    };
    return lightColors[color] || "#1A1A1E"; // Default Light Gray
}

// Function to apply styles to elements
function applyStyles(element, color, bgColor) {
    element.style.color = color;
    element.style.backgroundColor = bgColor;
    element.style.border = `1px solid ${color}`;
    element.style.padding = "2px";
    element.style.borderRadius = "5px";
}