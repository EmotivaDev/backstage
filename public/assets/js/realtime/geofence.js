let tableGeofences;
let PointsGeofences = [];
let polygonsGeofence = [];
let drawingManager;
let overlays = {};
let Geofences;

function parametersGeofence() {
    drawingManager = new google.maps.drawing.DrawingManager({
        drawingMode: null,
        drawingControl: true,

        drawingControlOptions: {
            position: google.maps.ControlPosition.TOP_CENTER,
            drawingModes: [
                google.maps.drawing.OverlayType.MARKER,
                google.maps.drawing.OverlayType.CIRCLE,
                google.maps.drawing.OverlayType.POLYGON,
                google.maps.drawing.OverlayType.POLYLINE,
                google.maps.drawing.OverlayType.RECTANGLE,
            ],
        },
        markerOptions: {
            icon: "https://developers.google.com/maps/documentation/javascript/examples/full/images/beachflag.png",
        },
        circleOptions: {
            fillColor: "#ffff00",
            fillOpacity: 1,
            strokeWeight: 5,
            clickable: false,
            editable: true,
            zIndex: 1,
        },
    });

    drawingManager.setMap(map);

    google.maps.event.addListener(drawingManager, 'overlaycomplete', function (event) {
        let overlay = event.overlay;
        if (overlay instanceof google.maps.Polygon || overlay instanceof google.maps.Polyline) {
            PointsGeofences.push(overlay);
            drawingManager.setDrawingMode(null);
            document.getElementById('activatePolygonButtonGeofence').innerText = 'Saved points';
            document.getElementById('activatePolygonButtonGeofence').disabled = true;
        }
    });

    const buttonDraw = document.getElementById('activatePolygonButtonGeofence');

    buttonDraw.addEventListener('click', function () {
        let selectTypeGeofence = document.getElementById('select-type-geofence').value;
        if (selectTypeGeofence) {
            drawingManager.setDrawingMode(google.maps.drawing.OverlayType[selectTypeGeofence]);
            buttonDraw.innerText = 'Drawing...';
            buttonDraw.disabled = true;
        }
    });

    document.getElementById('cancelButtonGeofence').addEventListener('click', function () {
        drawingManager.setDrawingMode(null);

        for (let i = 0; i < PointsGeofences.length; i++) {
            PointsGeofences[i].setMap(null);
        }

        PointsGeofences = [];
        buttonDraw.innerText = 'Draw Points';
        buttonDraw.disabled = false;
    });
}

async function createGeofence() {
    const selectTypeGeofence = document.getElementById('select-type-geofence').value;
    let area;
    let name = document.getElementById("create_name_geofence").value;

    if (!PointsGeofences.length || !name) {
        tripToast("Incomplete fields.", "danger", "campaign", "Error");
        return;
    }

    if (selectTypeGeofence === 'POLYGON') {
        const polygon = PointsGeofences[0].getPath().getArray().map(point => `${point.lat()} ${point.lng()}`).join(', ');
        area = `POLYGON ((${polygon}))`;
    } else if (selectTypeGeofence === 'POLYLINE') {
        const lineString = PointsGeofences[0].getPath().getArray().map(point => `${point.lat()} ${point.lng()}`).join(', ');
        area = `LINESTRING (${lineString})`;
    }

    const formData = new URLSearchParams(new FormData(document.getElementById("formCreateGeofence")));
    formData.append("area", area);

    try {
        const response = await fetch("geofences/create", {
            method: "POST",
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: formData.toString(),
        });

        if (!response.ok) {
            throw new Error(`HTTP error! Status: ${response.status}`);
        }

        let data = await response.json();

        if (tableGeofences) {
            tableGeofences.destroy();
            tableGeofences = null;
        }

        document.getElementById('create_name_geofence').value = '';
        document.getElementById('create_description_geofence').value = '';
        document.getElementById('activatePolygonButtonGeofence').innerText = 'Draw Points';
        document.getElementById('activatePolygonButtonGeofence').disabled = false;

        clearGeofences();
        for (let i = 0; i < PointsGeofences.length; i++) {
            PointsGeofences[i].setMap(null);
        }
        getGeofences();

        PointsGeofences = [];

        tripToast(data[0], "success", "check", "Success");

    } catch (error) {
        console.error('Error creating geofence:', error);
    }
}

async function updateGeofence() {
    const formData = new URLSearchParams(new FormData(document.getElementById("formUpdateGeofence")));
    const params = new URLSearchParams(formData);
    const geofenceId = params.get("id");

    try {
        const response = await fetch(`geofences/${parseInt(geofenceId)}`, {
            method: "PUT",
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: formData.toString(),
        });

        if (!response.ok) {
            throw new Error(`HTTP error! Status: ${response.status}`);
        }

        let data = await response.json();

        if (tableGeofences) {
            tableGeofences.destroy();
            tableGeofences = null;
        }

        let myModal = bootstrap.Modal.getInstance($('#modal-form-update-geofence')[0]);
        myModal.hide();

        document.getElementById('create_name_geofence').value = '';
        document.getElementById('create_description_geofence').value = '';
        document.getElementById('activatePolygonButtonGeofence').innerText = 'Draw Points';
        document.getElementById('activatePolygonButtonGeofence').disabled = false;

        clearGeofences();
        for (let i = 0; i < PointsGeofences.length; i++) {
            PointsGeofences[i].setMap(null);
        }
        getGeofences();

        PointsGeofences = [];
    
        tripToast(data[0], "success", "check", "Success");

    } catch (error) {
        console.error('Error creating geofence:', error);
    }
}

document.querySelector('#table-geofences').addEventListener('click', async function (event) {
    if (event.target.classList.contains("deleteGeofence")) {
        let dataId = event.target.getAttribute('data-id');
        let csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        try {
            const response = await fetch(`/geofences/${dataId}`, {
                method: "DELETE",
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json',
                },
            });

            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }

            let data = await response.json();

            if (tableGeofences) {
                tableGeofences.destroy();
                tableGeofences = null;
            }

            clearGeofences();
            getGeofences();
            tripToast(data[0], "success", "check", "Success");

        } catch (error) {
            console.error('Error deleting geofence:', error);
            tripToast("An error occurred while deleting the geofence.", "danger", "campaign", "Error");
        }
    }

    if (event.target.classList.contains("geofence-checkbox")) {
        let dataId = event.target.getAttribute('data-id');
        const overlay = overlays[dataId];
        const checkbox = document.querySelector(`.form-check-input[data-id="${dataId}"]`);
        if (checkbox.checked) {
            const bounds = new google.maps.LatLngBounds();
            overlay.getPath().forEach(point => bounds.extend(point));
            map.fitBounds(bounds);
            overlay.setMap(map);
        } else {
            overlay.setMap(null);
        }
    }

    if (event.target.classList.contains("updateGeofence")) {
        let dataId = event.target.getAttribute('data-id');
        const geofence = Geofences.find(geofence => geofence.id == parseInt(dataId));
        document.getElementById('update_id_geofence').value = geofence.id;
        document.getElementById('update_name_geofence').value = geofence.name;
        document.getElementById('update_description_geofence').value = geofence.description;
        document.getElementById('select-update-type-geofence').value = geofence.area.split(" ")[0];
        let myModal = new bootstrap.Modal($('#modal-form-update-geofence')[0]);
        myModal.show();
    }

    if (event.target.classList.contains("updateAreaGeofence")) {
        let dataId = event.target.getAttribute('data-id');
        const overlay = findOverlayById(dataId);

        if (overlay) {
            polygonsGeofence.forEach(poly => poly.setEditable(false));
            overlay.setEditable(true);

            if (overlay instanceof google.maps.Polygon) {
                const bounds = new google.maps.LatLngBounds();
                overlay.getPath().forEach(point => bounds.extend(point));
                map.fitBounds(bounds);
            } else if (overlay instanceof google.maps.Polyline) {
                const bounds = new google.maps.LatLngBounds();
                overlay.getPath().forEach(point => bounds.extend(point));
                map.fitBounds(bounds);
            }

            tripToast("Geofence is now editable. Make your changes and click outside the geofence to finish.", "info", "edit", "Edit Mode");

            if (overlay instanceof google.maps.Polygon || overlay instanceof google.maps.Polyline) {
                google.maps.event.addListener(overlay.getPath(), 'set_at', () => saveUpdatedGeofence(dataId, overlay));
                google.maps.event.addListener(overlay.getPath(), 'insert_at', () => saveUpdatedGeofence(dataId, overlay));
            }
        }
    }
});

function wktToLatLngArray(wkt) {
    const coordinates = wkt
        .replace(/^LINESTRING \(|POLYGON \(\(/, '')
        .replace(/\)\)$/, '')
        .split(', ')
        .map(coord => {
            const [lat, lng] = coord.split(' ').map(parseFloat);
            return { lat, lng };
        });
    return coordinates;
}

function drawGeofence(response) {
    response.forEach(geofence => {
        const path = wktToLatLngArray(geofence.area);

        let overlay;
        if (geofence.area.startsWith('POLYGON')) {
            overlay = new google.maps.Polygon({
                paths: path,
                strokeColor: '#FF0000',
                strokeOpacity: 0.8,
                strokeWeight: 2,
                fillColor: '#FF0000',
                fillOpacity: 0.15,
                map: map,
                editable: false 
            });
        } else if (geofence.area.startsWith('LINESTRING')) {
            overlay = new google.maps.Polyline({
                path: path,
                strokeColor: '#0000FF',
                strokeOpacity: 0.8,
                strokeWeight: 2,
                map: map,
                editable: false 
            });
        }

        if (overlay) {
            polygonsGeofence.push(overlay);
            overlays[geofence.id] = overlay;
        }
    });
}

async function saveUpdatedGeofence(id, overlay) {
    const path = overlay.getPath().getArray().map(point => `${point.lat()} ${point.lng()}`).join(', ');
    const area = overlay instanceof google.maps.Polygon
        ? `POLYGON ((${path}))`
        : `LINESTRING (${path})`;

    try {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const response = await fetch(`/geofences/area/${parseInt(id)}`, {
            method: 'PUT',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ area: area }),
        });

        if (!response.ok) {
            throw new Error(`HTTP error! Status: ${response.status}`);
        }
       
        const data = await response.json();
        tripToast(data.message || "Geofence updated successfully.", "success", "check", "Success");
    } catch (error) {
        console.error('Error updating geofence:', error);
        tripToast("An error occurred while updating the geofence.", "danger", "campaign", "Error");
    }
}

function clearGeofences() {
    polygonsGeofence.forEach(polygon => {
        polygon.setMap(null);
    });
    polygonsGeofence = [];
}

function findOverlayById(id) {
    return overlays[id];
}

async function getGeofences() {
    let csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    const formatText = (text) => {
        if (text && text.length > 0) {
            return text.length > 15 ? text.substring(0, 15).toUpperCase() + '...' : text.toUpperCase();
        }
        return '';
    };

    try {
        const response = await fetch('geofences', {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Content-Type': 'application/json',
            },
        });

        if (!response.ok) {
            throw new Error(`HTTP error! Status: ${response.status}`);
        }

        Geofences = await response.json();

        let newDataArray = Geofences.map(item => ({
            "Name": `
            <div class="d-flex align-items-center">
                <div class="form-check">
                    <input class="form-check-input geofence-checkbox" type="checkbox" data-id="${item.id}" checked>
                </div>
                <p class="text-xs font-weight-normal ms-2 mb-0" data-bs-toggle="tooltip" title="${formatText(item.description)}"> ${formatText(item.name)}</p>
            </div>
            `,
            "Actions": `
                <div class="text-center">
                 <a href="#" data-bs-toggle="tooltip" title="Edit">
                        <i class="material-symbols-rounded text-secondary position-relative text-lg updateGeofence" data-id="${item.id}">drive_file_rename_outline</i>
                    </a>
                     <a href="#" data-bs-toggle="tooltip" title="Edit Area">
                        <i class="material-symbols-rounded text-secondary position-relative text-lg updateAreaGeofence" data-id="${item.id}">app_registration</i>
                    </a>
                    <a href="#" data-bs-toggle="tooltip" title="Delete">
                        <i class="material-symbols-rounded text-secondary position-relative text-lg deleteGeofence" data-id="${item.id}">delete</i>
                    </a>
                </div>
            `,
        }));

        // Initialize the DataTable
        tableGeofences = new simpleDatatables.DataTable("#table-geofences", {
            searchable: true,
            perPage: 3,
        });

        tableGeofences.insert(newDataArray);
        drawGeofence(Geofences);

    } catch (error) {
        console.error('Error fetching geofence data:', error);
    }
}

