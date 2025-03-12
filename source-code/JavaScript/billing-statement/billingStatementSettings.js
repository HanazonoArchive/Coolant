document.addEventListener("DOMContentLoaded", () => {
    const saveBtn = document.getElementById("saveSettings");
    const loadBtn = document.getElementById("loadSettings");
    const updateBtn = document.getElementById("updateSettings");
    const deleteBtn = document.getElementById("deleteSettings");
    const settingsNameInput = document.getElementById("settingName");
    const dropdown = document.getElementById("settingsLoad");

    // Change the storage key name here if needed (e.g., "invoice", "profileSettings")
    const STORAGE_KEY = "billingStatement"; 

    function getStorage() {
        return JSON.parse(localStorage.getItem(STORAGE_KEY)) || [];
    }
    
    function saveStorage(data) {
        localStorage.setItem(STORAGE_KEY, JSON.stringify(data));
    }

    function updateDropdown() {
        dropdown.innerHTML = "";
        const settings = getStorage();
        settings.forEach(setting => {
            const option = document.createElement("sl-option");
            option.value = setting.id;
            option.textContent = setting.name;
            dropdown.appendChild(option);
        });
    }

    function getFormData() {
        const formData = new FormData();
        document.querySelectorAll("[data-save]").forEach(input => {
            formData.append(input.id, input.value);
        });
        return Object.fromEntries(formData);
    }

    function setFormData(content) {
        document.querySelectorAll("[data-save]").forEach(input => {
            input.value = content[input.id] || "";
        });
    }

    saveBtn.addEventListener("click", () => {
        const settings = getStorage();
        const newId = settings.length > 0 ? settings[settings.length - 1].id + 1 : 1;
        const name = settingsNameInput.value.trim();
        if (!name) return alert("Enter a settings name");
        
        const content = getFormData();

        settings.push({ id: newId, name, content });
        saveStorage(settings);
        updateDropdown();
    });

    loadBtn.addEventListener("click", () => {
        const settings = getStorage();
        const selectedId = parseInt(dropdown.value);
        const setting = settings.find(s => s.id === selectedId);
        if (setting) {
            settingsNameInput.value = setting.name;
            setFormData(setting.content);
        }
    });

    updateBtn.addEventListener("click", () => {
        let settings = getStorage();
        const selectedId = parseInt(dropdown.value);
        const settingIndex = settings.findIndex(s => s.id === selectedId);
        if (settingIndex !== -1) {
            settings[settingIndex].name = settingsNameInput.value.trim();
            settings[settingIndex].content = getFormData();
            saveStorage(settings);
            updateDropdown();
        }
    });

    deleteBtn.addEventListener("click", () => {
        let settings = getStorage();
        const selectedId = parseInt(dropdown.value);
        settings = settings.filter(s => s.id !== selectedId);
        saveStorage(settings);
        updateDropdown();
    });

    updateDropdown();
});
