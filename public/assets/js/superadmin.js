if (document.getElementById('tc-users-list')) {
    new simpleDatatables.DataTable("#tc-users-list", {
        searchable: true,
        fixedHeight: false,
        perPage: 7
    });
};

if (document.getElementById('tc-devices-list')) {
    new simpleDatatables.DataTable("#tc-devices-list", {
        searchable: true,
        fixedHeight: false,
        perPage: 7
    });
};

function confirmDeleteDevice(event, url) {
    event.preventDefault();
    if (confirm(translations.confirm_delete)) {
        var form = document.getElementById('deleteFormDevice');
        form.action = url;
        form.submit();
    }
}