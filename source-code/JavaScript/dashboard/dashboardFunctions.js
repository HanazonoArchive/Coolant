document.addEventListener("DOMContentLoaded", function () {
    updateEmployeeStats();
    updateAppointmentStats();
});

function updateEmployeeStats() {
    fetch("/Coolant/source-code/Controller/dashboardController.php?employeeStats=true")
        .then(response => response.json())
        .then(data => {
            if (!data.error) {
                const available = data.availableEmployees ?? 0;
                const total = data.totalEmployees ?? 0;
                const percentage = total > 0 ? ((available / total) * 100).toFixed(1) : 0;

                const progressRing = document.getElementById("employeeProgress");
                progressRing.value = percentage;
                progressRing.textContent = `${available}/${total}`;
            }
        })
        .catch(error => console.error("Error fetching employee stats:", error));
}

function updateAppointmentStats() {
    fetch("/Coolant/source-code/Controller/dashboardController.php?appointmentStats=true")
        .then(response => response.json())
        .then(data => {
            if (!data.error) {
                const completed = data.completedAppointments ?? 0;
                const total = data.totalAppointments ?? 0;
                const percentage = total > 0 ? ((completed / total) * 100).toFixed(1) : 0;

                const progressRing = document.getElementById("appointmentProgress");
                progressRing.value = percentage;
                progressRing.textContent = `${completed}/${total}`;
            }
        })
        .catch(error => console.error("Error fetching appointment stats:", error));
}
