document.addEventListener("DOMContentLoaded", function () {
    updateEmployeeStats();
    updateAppointmentStats();
});

function updateEmployeeStats() {
    fetch("/Coolant/source-code/Controller/dashboardController.php?employeeStats=true")
        .then(response => response.json())
        .then(data => {
            if (!data.error) {
                const percentage = (data.availableEmployees / data.totalEmployees) * 100;
                document.getElementById("employeeCount").textContent = `${data.availableEmployees}/${data.totalEmployees}`;
                document.getElementById("employeeProgress").style.background = `conic-gradient(var(--optional-color) ${percentage}%, #ddd ${percentage}%)`;
            }
        })
        .catch(error => console.error("Error fetching employee stats:", error));
}

function updateAppointmentStats() {
    fetch("/Coolant/source-code/Controller/dashboardController.php?appointmentStats=true")
        .then(response => response.json())
        .then(data => {
            if (!data.error) {
                const percentage = (data.completedAppointments / data.totalAppointments) * 100;
                document.getElementById("appointmentCount").textContent = `${data.completedAppointments}/${data.totalAppointments}`;
                document.getElementById("appointmentProgress").style.background = `conic-gradient(var(--optional-color) ${percentage}%, #ddd ${percentage}%)`;
            }
        })
        .catch(error => console.error("Error fetching appointment stats:", error));
}
