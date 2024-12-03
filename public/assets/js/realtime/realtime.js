function initMap() {
    map = new google.maps.Map(document.getElementById('map'), {
        zoom: 15,
        center: { lat: 11.015256094174626, lng: -74.82771887080165 },
        styles: [
            {
                featureType: "water",
                stylers: [{ color: "#6eb9fb" }],
            },
        ],
        mapTypeControl: false,
        zoomControl: false,
        streetViewControl: false,
        fullscreenControl: false
    });
    const drawingManager = new google.maps.drawing.DrawingManager({
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
            let path = overlay.getPath();
            path.forEach(function (point) {
                points_geofence.push({
                    lat: point.lat(),
                    lng: point.lng()
                });
            });
        }

    });


    document.getElementById('activatePolygonButton').addEventListener('click', function () {
        let selectedMode = document.getElementById('select-geofence').value;
        if (selectedMode) {

            drawingManager.setDrawingMode(google.maps.drawing.OverlayType[selectedMode]);
        }
    });

    document.getElementById('cancelButton').addEventListener('click', function () {
        drawingManager.setDrawingMode(null);
        points_geofence = [];
    });

    connectWebsockets();
}

let arrayDevices = [];
let markers = [];
let polylineSegments = [];
let layers  = {};
let map, infoWindow, markerSelected;
let deviceSelected;

async function connectWebsockets() {
    let csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    try {
        let response = await fetch('/traccar/token', {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
            credentials: 'include',
        });

        if (!response.ok) {
            throw new Error(`Error ${response.status}: ${response.statusText}`);
        }

        let data = await response.json();

        var fetchRequest = function (method, urlWebSockets) {
            return fetch(urlWebSockets, {
                method: method,
                headers: method === 'POST' ? { 'Content-type': 'application/json' } : {},
                credentials: 'include'
            })
                .then(response => response.json());
        };

        fetchRequest('GET', urlWebSockets + '/api/session?token=' + data.token)
            .then(user => {
                return fetchRequest('GET', urlWebSockets + '/api/devices');
            })
            .then(devices => {
                return fetchRequest('GET', urlWebSockets + '/api/positions')
                    .then(positions => {
                        arrayDevices = positions.map(position => {
                            const device = devices.find(dev => dev.id === position.deviceId);
                            const trip = listTrips.find(trip => trip.deviceid === position.deviceId);
                            let closestPosition = findClosestPosition(position.latitude, position.longitude);
                            return {
                                ...position,
                                name: device ? device.name : null,
                                speed: parseFloat((position.speed * 1.852).toFixed(2)),
                                course: position.course + courseFormatter(position.course),
                                location: closestPosition.location ? closestPosition.location.toUpperCase() : null,
                                milemark: closestPosition.milemark ?? null,
                                description: closestPosition.description ?? null,
                                contact: device.contact ? JSON.parse(device.contact) : null,
                                trip: trip,
                                positionsLog: (PositionsLog && PositionsLog[device.id]) || []
                            };
                        });
                        addMarkers(arrayDevices);
                        writeDevicesList(arrayDevices);
                        writeDevicesListLayer(arrayDevices);
                        initLayers();
                        infoWindow = new google.maps.InfoWindow();
                    });
            })
            .then(() => {
                const socket = new WebSocket('ws' + urlWebSockets.substring(4) + '/api/socket');

                socket.onopen = () => {
                    console.log('Socket Open');
                };

                socket.onerror = (error) => {
                    console.log('Socket Error: ', error);
                };

                socket.onclose = (event) => {
                    console.log('Socket Closed:', event);
                };

                socket.onmessage = (event) => {
                    let data = JSON.parse(event.data);
                    if (data.positions) {
                        let position = data.positions[0];
                        updateDevice(position);
                    }
                };
            })
            .catch(error => {
                console.error('Error:', error);
            });


    } catch (error) {
        console.error('Error token:', error);
    }
}

function updateDevice(position) {
    let device = arrayDevices.find(d => d.deviceId === position.deviceId);

    if (device) {
        let closestPosition = findClosestPosition(position.latitude, position.longitude);

        device.latitude = position.latitude;
        device.longitude = position.longitude;
        device.speed = parseFloat((position.speed * 1.852).toFixed(2));
        device.course = position.course + courseFormatter(position.course);
        device.trip = JSON.parse(position.trip);
        device.location = closestPosition.location ? closestPosition.location.toUpperCase() : null;
        device.milemark = closestPosition.milemark ?? null;
        device.description = closestPosition.description ?? null;

        device.positionsLog.push({
            attributes: position.attributes,
            date: position.deviceTime,
            lat: position.latitude,
            lng: position.longitude,
            speed: parseFloat((position.speed * 1.852).toFixed(2))
        });

        updateMarker(device);
        updateNewPolyline(device.positionsLog);

        if (device.deviceId === deviceSelected.deviceId) {
            updateNavigation(device);
            updateTelemetry(device);
            showTrip();
        }
    }
}


function formatDate(deviceTime) {
    if (!deviceTime) return "-";
    const date = new Date(deviceTime);

    const options = {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit',
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit',
        hour12: false,
        timeZone: timeZone
    };

    const formattedDate = date.toLocaleString('en-US', options);

    const [datePart, timePart] = formattedDate.split(', ');
    const [month, day, year] = datePart.split('/');
    const [hour, minute, second] = timePart.split(':');

    return `${year}-${month}-${day} ${hour}:${minute}:${second}`;
}

function updateNavigation(device) {
    let locationTelemetry = device.location + "-" + device.milemark + (device.description ? " - " + device.description : "");
    const arrayCards = [
        { id: 'date_device_navigation', value: formatDate(device.deviceTime) || "-", maxChars: 20 },
        { id: 'name_device_nav', value: device.name || "-", maxChars: 15 },
        { id: 'altitude_device_navigation', value: device.altitude || "-", maxChars: 10 },
        { id: 'odometer_device_navigation', value: device.odometer || "-", maxChars: 10 },
        { id: 'fuel_device_navigation', value: device.fuel || "-", maxChars: 10 },
        { id: 'fuel_tank_device_navigation', value: device.fuel_tank || "-", maxChars: 10 },
        { id: 'eta_device_navigation', value: device.eta || "-", maxChars: 10 },
        { id: 'power_device_navigation', value: device.power || "-", maxChars: 10 },
        { id: 'speed_device_navigation', value: device.speed + " km/h" || "-", maxChars: 10 },
        { id: 'course_device_navigation', value: device.course || "-", maxChars: 10 },
        { id: 'event_device_navigation', value: device.event || "-", maxChars: 10 },
        { id: 'location_device_navigation', value: device.location + "-" + device.milemark || "-", maxChars: 10 },
        { id: 'speed_device_telemetry', value: device.speed + " km/h" || "-", maxChars: 10 },
        { id: 'course_device_telemetry', value: device.course || "-", maxChars: 10 },
        { id: 'event_device_telemetry', value: device.event || "-", maxChars: 10 },
        { id: 'location_device_telemetry', value: locationTelemetry || "-", maxChars: 30 }
    ];

    arrayCards.forEach(cards => {
        updateText(cards.id, cards.value, cards.maxChars);
    });
}

document.querySelectorAll("#nav-tab-principal button.nav-link").forEach(function (tabPrincipal) {
    tabPrincipal.addEventListener("shown.bs.tab", function (event) {
        let tabContentId = event.target.getAttribute('id');
        let enginesNav = document.getElementById('nav_engines');
        showOrHideNavEngines(deviceSelected);
        if (tabContentId == "nav-telemetry-tab") {
            enginesNav.style.display = "block";
            updateTelemetry(deviceSelected);
        } else if (tabContentId == "nav-voyage-plan-tab") {
            enginesNav.style.display = "none";
            showTrip();
        } else {
            enginesNav.style.display = "none";
        }
    });
});

document.getElementById("nav-tab-secondary").addEventListener("click", function (event) {
    if (event.target && event.target.matches("button.nav-link")) {
        updateTelemetry(deviceSelected);
    }
});

function updateTelemetry(device) {
    let tabEngines = document.querySelector('#nav-tab-secondary .nav-link.active');
    const tabMapping = {
        "nav-engine1-tab": 1,
        "nav-engine2-tab": 2,
        "nav-engine3-tab": 3,
        "nav-engine4-tab": 4,
        "nav-engine5-tab": 5
    };

    let tabIdEngine = tabMapping[tabEngines.id] || null;

    if (tabIdEngine != null && device.attributes && device.attributes.emi != null) {
        let emi = device.attributes.emi;
        let arrayEngine = device.contact;
        if (emi && arrayEngine && arrayEngine.includes("E" + tabIdEngine)) {
            let emiData = JSON.parse(device.attributes.emi);
            const arrayCards = [
                { id: 'depth_device_navigation', value: emiData.depth || "-", maxChars: 10 },
                { id: 'rpm_device_telemetry', value: emiData['rpm' + tabIdEngine] ?? "-", maxChars: 15 },
                { id: 'coolant_device_telemetry', value: emiData['ect' + tabIdEngine] ? emiData['ect' + tabIdEngine] + "ºC" : "-", maxChars: 10 },
                { id: 'oil_device_telemetry', value: emiData['eop' + tabIdEngine] ? emiData['eop' + tabIdEngine] + "psi" : "-", maxChars: 10 },
                {
                    id: 'total_fuel_device_telemetry', value: (emiData['tfuel' + tabIdEngine] ?? null) !== null
                        ? ((emiData['tfuel' + tabIdEngine] ?? 0) / 3.785).toFixed(1)
                        : "-", maxChars: 10
                },
                { id: 'hours_device_telemetry', value: emiData['hours' + tabIdEngine] ?? "-", maxChars: 10 },
                { id: 'batt_device_telemetry', value: emiData['vol' + tabIdEngine] != null ? emiData['vol' + tabIdEngine] + "V" : "-", maxChars: 10 },
                { id: 'odometer_device_telemetry', value: device.attributes.odometer ?? "-", maxChars: 10 },
                { id: 'odometer_device_navigation', value: device.attributes.odometer ?? "-", maxChars: 10 },
                {
                    id: 'fuel_rate_device_telemetry', value: (emiData['rfuel' + tabIdEngine] != null && emiData['sfuel' + tabIdEngine] != null)
                        ? emiData['rfuel' + tabIdEngine] + "(" + emiData['sfuel' + tabIdEngine] + ")"
                        : "-", maxChars: 10
                },
            ];

            arrayCards.forEach(cards => {
                updateText(cards.id, cards.value, cards.maxChars);
            });
        }
    } else {
        const arrayCards = [
            { id: 'depth_device_navigation', value: "-", maxChars: 10 },
            { id: 'rpm_device_telemetry', value: "-", maxChars: 15 },
            { id: 'coolant_device_telemetry', value: "-", maxChars: 10 },
            { id: 'oil_device_telemetry', value: "-", maxChars: 10 },
            { id: 'total_fuel_device_telemetry', value: "-", maxChars: 10 },
            { id: 'hours_device_telemetry', value: "-", maxChars: 10 },
            { id: 'batt_device_telemetry', value: "-", maxChars: 10 },
            { id: 'odometer_device_telemetry', value: "-", maxChars: 10 },
            { id: 'odometer_device_navigation', value: "-", maxChars: 10 },
            { id: 'fuel_rate_device_telemetry', value: "-", maxChars: 10 },
        ];
        arrayCards.forEach(cards => {
            updateText(cards.id, cards.value, cards.maxChars);
        });
    }
}

function updateText(id, value, maxChars = null) {
    const textHtml = document.getElementById(id);
    if (textHtml) {
        if (maxChars && value.length > maxChars) {
            value = value.substring(0, maxChars);
        }
        textHtml.textContent = value;
    }
}


function changeSelectedDevice(cardDevices) {
    const selectedDeviceCard = document.querySelector(`.${cardDevices}.selected`);

    if (selectedDeviceCard) {
        let deviceId = selectedDeviceCard.getAttribute("data-id");
        deviceSelected = arrayDevices.find(d => d.deviceId === parseInt(deviceId));
    }
}

// click in the list Device
document.addEventListener("deviceSelectionChanged", function (event) {
    changeSelectedDevice("device-card");
    selectDeviceById(deviceSelected, "device-card-layer");
    updateText("title_device_selected", deviceSelected.name.toUpperCase().substring(0, 15));
    showOrHideNavEngines(deviceSelected);
    updateNavigation(deviceSelected);
    updateTelemetry(deviceSelected);
    showTrip();

    var marker = markers[deviceSelected.deviceId];
    if (marker) {
        // infoWindow.close();
        map.panTo(marker.getPosition());
        map.setZoom(15);
    }
});

function writeDevicesList(devices) {
    const deviceListElement = document.getElementById("deviceList");
    devices.forEach(device => {
        const deviceCard = document.createElement("div");
        deviceCard.classList.add("device-card");
        deviceCard.setAttribute("data-id", device.deviceId);

        const deviceInfo = document.createElement("div");
        deviceInfo.classList.add("device-info");

        const deviceName = document.createElement("div");
        deviceName.classList.add("device-name");
        deviceName.textContent = device.name.toUpperCase().substring(0, 15);

        const deviceDetails = document.createElement("div");
        deviceDetails.classList.add("d-flex");

        const deviceTrip = document.createElement("span");
        deviceTrip.classList.add("device-trip");

        const deviceSpeed = document.createElement("span");
        deviceSpeed.classList.add("device-speed");
        deviceSpeed.textContent = device.speed + " km/h";

        const deviceStatus = document.createElement("span");
        deviceStatus.classList.add("device-status");
        deviceStatus.textContent = "";

        deviceDetails.append(deviceTrip, deviceSpeed, deviceStatus);
        deviceInfo.append(deviceName, deviceDetails);

        const location = document.createElement("div");
        location.classList.add("device-location");

        deviceCard.append(deviceInfo, location);
        deviceListElement.appendChild(deviceCard);

        deviceCard.addEventListener("click", function () {
            const selectedDevice = document.querySelector(".device-card.selected");
            if (selectedDevice) {
                selectedDevice.classList.remove("selected");
            }
            deviceCard.classList.add("selected");

            const changeEvent = new CustomEvent("deviceSelectionChanged", {
            });
            document.dispatchEvent(changeEvent);
        });

    });

    if (devices.length > 0) {
        const firstDeviceCard = deviceListElement.querySelector(".device-card");
        if (firstDeviceCard) {
            firstDeviceCard.click();
        }
    }
}


document.addEventListener("deviceLayerSelectionChanged", function (event) {
    changeSelectedDevice("device-card-layer");
    selectDeviceById(deviceSelected, "device-card");
    updateText("title_device_selected", deviceSelected.name.toUpperCase().substring(0, 15));
    showOrHideNavEngines(deviceSelected);
    updateNavigation(deviceSelected);
    updateTelemetry(deviceSelected);
    showTrip();

    var marker = markers[deviceSelected.deviceId];
    if (marker) {
        // infoWindow.close();
        map.panTo(marker.getPosition());
        map.setZoom(15);
    }
});

function writeDevicesListLayer(devices) {
    const deviceListLayerElement = document.getElementById("deviceListLayer");
    devices.forEach(device => {
        const deviceCard = document.createElement("div");
        deviceCard.classList.add("device-card-layer");
        deviceCard.setAttribute("data-id", device.deviceId);

        const deviceInfo = document.createElement("div");
        deviceInfo.classList.add("device-info");

        const deviceName = document.createElement("div");
        deviceName.classList.add("device-name-layer");
        deviceName.textContent = device.name.toUpperCase().substring(0, 15);

        const deviceDetails = document.createElement("div");
        deviceDetails.classList.add("d-flex");

        const deviceTrip = document.createElement("span");
        deviceTrip.classList.add("device-trip-layer");

        const deviceSpeed = document.createElement("span");
        deviceSpeed.classList.add("device-speed-layer");
        deviceSpeed.textContent = device.speed + " km/h";

        const deviceStatus = document.createElement("span");
        deviceStatus.classList.add("device-status-layer");
        deviceStatus.textContent = "";

        deviceDetails.append(deviceTrip, deviceSpeed, deviceStatus);
        deviceInfo.append(deviceName, deviceDetails);

        const location = document.createElement("div");
        location.classList.add("device-location-layer");

        deviceCard.append(deviceInfo, location);
        deviceListLayerElement.appendChild(deviceCard);
        deviceCard.addEventListener("click", function () {
            const selectedDevice = document.querySelector(".device-card-layer.selected");
            if (selectedDevice) {
                selectedDevice.classList.remove("selected");
            }
            deviceCard.classList.add("selected");

            const changeEvent = new CustomEvent("deviceLayerSelectionChanged", {
            });
            document.dispatchEvent(changeEvent);
        });

    });

    checkAliveDevices();
    if (devices.length > 0) {
        const firstDeviceCard = deviceListLayerElement.querySelector(".device-card-layer");
        if (firstDeviceCard) {
            firstDeviceCard.click();
        }
    }
}

function selectDeviceById(device, cardDevices) {
    const deviceCard = document.querySelector(`.${cardDevices}[data-id='${device.deviceId}']`);

    if (deviceCard) {
        const selectedDevice = document.querySelector(`.${cardDevices}.selected`);
        if (selectedDevice) {
            selectedDevice.classList.remove("selected");
        }

        deviceCard.classList.add("selected");

    }
}

function addMarkers(devices) {
    devices.forEach(device => {
        let locationTelemetry = device.location + "-" + device.milemark;
        var marker = new google.maps.Marker({
            position: { lat: device.latitude, lng: device.longitude },
            map: map,
            title: device.name,
            icon: {
                path: google.maps.SymbolPath.FORWARD_CLOSED_ARROW,
                scale: 5,
                rotation: parseInt(device.course.match(/\d+/)[0], 10),
                strokeColor: "gold",
            },
            label: {
                text: device.name + "-" + device.speed + "km/h-" +
                    locationTelemetry,
                fontWeight: "bold",
                fontSize: "12px",
                fontFamily: '"Courier New", Courier,Monospace',
                color: "black",
            },
        });
        markers[device.deviceId] = marker;
        createPolyline(device.positionsLog);
    });

    new MarkerClusterer(map, Object.values(markers),
        {
            imagePath: 'https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/m',
            gridSize: 60,
            maxZoom: 10,
        });
}

function updateMarker(device) {
    var marker = markers[device.deviceId];
    let locationTelemetry = device.location + "-" + device.milemark;
    if (marker) {
        var icon = marker.getIcon();
        if (icon) {
            icon.rotation = parseInt(device.course.match(/\d+/)[0], 10);
            marker.setIcon(icon);
        }
        marker.setTitle(device.name);
        var labelText = device.name + "-" + device.speed + "km/h-" + locationTelemetry;
        marker.setLabel({
            text: labelText,
            fontWeight: "bold",
            fontSize: "12px",
            fontFamily: '"Courier New", Courier,Monospace',
            color: "black"
        });
        marker.setPosition({ lat: parseFloat(device.latitude), lng: parseFloat(device.longitude) });

    }
}


/* Trips */

async function createTrip(event) {
    event.preventDefault();

    let form = document.getElementById('createTripForm');
    let formData = new FormData(form);
    let device = deviceSelected;

    formData.append('deviceid', device.deviceId);

    try {
        const response = await fetch('/trip/create', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            }
        });

        const data = await response.json();

        if (data.success) {
            form.reset();
            device.trip = await getTrip();
            checkAliveDevices();
            showTrip();
            tripToast(data[0], "success", "check", "Success");
            $('#modal-form-create-trip').modal('hide');
        } else {
            tripToast(data.message, "danger", "campaign", "Error");
        }
    } catch (error) {
        console.error('Error:', error);
    }
}

function showTrip() {
    let device = deviceSelected;
    let buttonCreateTrip = document.getElementById('button_create_trip');
    let buttonUpdateTrip = document.getElementById('button_update_trip');
    let buttonFinishTrip = document.getElementById('button_finish_trip');

    if (device.trip && device.trip.number !== null) {
        buttonCreateTrip.style.display = "none";
        buttonUpdateTrip.style.display = "";
        buttonFinishTrip.style.display = "";

        const arrayCards = [
            { id: 'view_number_trip', value: device.trip.number || "-", maxChars: 10 },
            { id: 'view_origin_trip', value: device.trip.origin === 'null' ? '-' : (device.trip.origin || '-'), maxChars: 10 },
            { id: 'view_destination_trip', value: (device.trip.destination === 'null' || device.trip.destination == null) ? "-" : device.trip.destination, maxChars: 10 },
            { id: 'view_draft_trip', value: (device.trip.draft === 'null' || device.trip.draft == null) ? "-" : device.trip.draft, maxChars: 10 },
            { id: 'view_bargues_trip', value: (device.trip.bargues === 'null' || device.trip.bargues == null) ? "-" : device.trip.bargues, maxChars: 10 },
            { id: 'view_load_type_trip', value: (device.trip.loadtype === 'null' || device.trip.loadtype == null) ? "-" : device.trip.loadtype, maxChars: 10 },
            { id: 'view_tonnes_trip', value: (device.trip.tonnes === 'null' || device.trip.tonnes == null) ? "-" : device.trip.tonnes, maxChars: 10 },
            { id: 'view_description_trip', value: (device.trip.description === 'null' || device.trip.description == null) ? "-" : device.trip.description, maxChars: 10 }
        ];

        arrayCards.forEach(cards => {
            updateText(cards.id, cards.value, cards.maxChars);
        });
    } else {
        buttonCreateTrip.style.display = "";
        buttonUpdateTrip.style.display = "none";
        buttonFinishTrip.style.display = "none";

        const arrayCards = [
            { id: 'view_number_trip', value: "-", maxChars: 10 },
            { id: 'view_origin_trip', value: "-", maxChars: 10 },
            { id: 'view_destination_trip', value: "-", maxChars: 10 },
            { id: 'view_draft_trip', value: "-", maxChars: 10 },
            { id: 'view_bargues_trip', value: "-", maxChars: 10 },
            { id: 'view_load_type_trip', value: "-", maxChars: 10 },
            { id: 'view_tonnes_trip', value: "-", maxChars: 10 },
            { id: 'view_description_trip', value: "-", maxChars: 10 },
        ];
        arrayCards.forEach(cards => {
            updateText(cards.id, cards.value, cards.maxChars);
        });
    }
}

function getTrip() {
    let device = deviceSelected;

    return fetch('/trip/' + device.deviceId, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
        }
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                return data.trip;
            } else {
                throw new Error('Trip data not found');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            throw error;
        });
}

function editTrip() {
    let device = deviceSelected;
    if (device.trip && device.trip.number !== null) {
        document.getElementById('update_number_trip').value = (device.trip.number === null) ? "" : device.trip.number;
        $('#choices-update_origin-trip').val(device.trip.origin || "").selectpicker('refresh');
        $('#choices-update_destination-trip').val(device.trip.destination || "").selectpicker('refresh');
        document.getElementById('update_draft_trip').value = (device.trip.draft === null || device.trip.draft === "") ? "" : device.trip.draft;
        document.getElementById('update_bargues_trip').value = (device.trip.bargues === null || device.trip.bargues === "") ? "" : device.trip.bargues;
        $('#choices-update_load_type-trip').val(device.trip.loadtype || "").selectpicker('refresh');
        document.getElementById('update_tonnes_trip').value = (device.trip.tonnes === null || device.trip.tonnes === "") ? "" : device.trip.tonnes;
        document.getElementById('update_description_trip').value = (device.trip.description === null || device.trip.description === "") ? "" : device.trip.description;
    } else {
        document.getElementById('update_number_trip').value = "-";
        document.getElementById('update_draft_trip').value = "-";
        document.getElementById('update_bargues_trip').value = "-";
        document.getElementById('update_tonnes_trip').value = "-";
        document.getElementById('update_description_trip').value = "-";
    }
}

async function updateTrip(event) {
    event.preventDefault();

    let form = document.getElementById('updateTripForm');
    let formData = new FormData(form);
    let device = deviceSelected;

    try {
        let response = await fetch('/trip/update/' + device.deviceId, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            }
        });

        let data = await response.json();

        if (data.success) {
            form.reset();
            device.trip = await getTrip();
            showTrip();

            tripToast(data[0], "success", "check", "Success");
            $('#modal-form-update-trip').modal('hide');
        } else {
            tripToast(data.message, "danger", "campaign", "Error");
        }
    } catch (error) {
        console.error('Error:', error);
        tripToast("Something went wrong", "danger", "campaign", "Error");
    }
}

async function finishTrip() {
    let device = deviceSelected;
    let csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    try {
        let response = await fetch('/trip/' + device.deviceId, {
            method: 'DELETE',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            }
        });

        let data = await response.json();

        if (data.success) {
            device.trip = await getTrip();
            checkAliveDevices();
            showTrip();
            tripToast(data[0], "success", "check", "Success");
            $('#modal-form-finish-trip').modal('hide');
        } else {
            tripToast(data.message, "danger", "campaign", "Error");
        }
    } catch (error) {
        console.error('Error:', error);
        tripToast("Something went wrong", "danger", "campaign", "Error");
    }
}


function findClosestPosition(latitude, longitude) {
    let closestPosition = null;
    let minDistance = Infinity;

    for (const posB of listReverseGeocodes) {
        const distance = haversineDistance(
            latitude,
            longitude,
            posB.latitude,
            posB.longitude
        );
        if (distance < minDistance) {
            minDistance = distance;
            closestPosition = posB;
        }
    }
    return closestPosition;
}

function haversineDistance(lat1, lon1, lat2, lon2) {
    const R = 6371;
    const dLat = ((lat2 - lat1) * Math.PI) / 180;
    const dLon = ((lon2 - lon1) * Math.PI) / 180;
    const a =
        Math.sin(dLat / 2) * Math.sin(dLat / 2) +
        Math.cos((lat1 * Math.PI) / 180) *
        Math.cos((lat2 * Math.PI) / 180) *
        Math.sin(dLon / 2) *
        Math.sin(dLon / 2);
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
    return R * c;
}

function courseFormatter(course) {
    var heading;

    if (course >= 0 && course <= 22.5) {
        heading = "↑";
    }
    if (course > 22.5 && course <= 67.5) {
        heading = "↑→";
    }
    if (course > 67.5 && course <= 112.5) {
        heading = "→";
    }
    if (course > 112.5 && course <= 157.5) {
        heading = "↓→";
    }
    if (course > 157.5 && course <= 202.5) {
        heading = "↓";
    }
    if (course > 202.5 && course <= 247.5) {
        heading = "←↓";
    }
    if (course > 247.5 && course <= 292.5) {
        heading = "←";
    }
    if (course > 292.5 && course <= 337.5) {
        heading = "←↑";
    }
    if (course > 337.5 && course <= 360.1) {
        heading = "↑";
    }

    return " (" + heading + ")";
}


function checkAliveDevices() {
    arrayDevices.forEach(function (device) {
        let deviceCard = document.querySelector(`.device-card[data-id="${device.deviceId}"]`);
        let deviceCardLayer = document.querySelector(`.device-card-layer[data-id="${device.deviceId}"]`);

        let locationTelemetry = device.location + "-" + device.milemark + (device.description ? " - " + device.description : "");

        deviceCard.querySelector('.device-location').textContent = locationTelemetry;
        deviceCardLayer.querySelector('.device-location-layer').textContent = locationTelemetry;

        let tripStatus = device.trip && device.trip.number !== null ? "Trip" : null;
        let tripStatusHidden = device.trip && device.trip.number !== null ? false : true;

        deviceCard.querySelector('.device-trip').textContent = tripStatus;
        deviceCard.querySelector('.device-trip').hidden = tripStatusHidden;

        deviceCardLayer.querySelector('.device-trip-layer').textContent = tripStatus;
        deviceCardLayer.querySelector('.device-trip-layer').hidden = tripStatusHidden;

        let dateFormat = formatDate(device.deviceTime);
        let fixedDate = new Date(dateFormat);
        let currentDate = new Date();
        let difference = Math.abs(currentDate - fixedDate);

        deviceCard.querySelector('.device-status').classList.remove("badge-warning", "badge-success", "badge-danger");
        deviceCardLayer.querySelector('.device-status-layer').classList.remove("badge-warning", "badge-success", "badge-danger");

        if (!device.deviceTime) {
            deviceCard.querySelector('.device-status').textContent = 'Offline';
            deviceCard.querySelector('.device-status').classList.add("badge-danger");

            deviceCardLayer.querySelector('.device-status-layer').textContent = 'Offline';
            deviceCardLayer.querySelector('.device-status-layer').classList.add("badge-danger");
        } else if (difference < 60000) {
            let typeStatusColor = device.speed === 0 ? "badge-warning" : "badge-success";
            let typeStatus = device.speed === 0 ? "Stopped" : "Moving";
            deviceCard.querySelector('.device-status').textContent = typeStatus;
            deviceCard.querySelector('.device-status').classList.add(typeStatusColor);

            deviceCardLayer.querySelector('.device-status-layer').textContent = typeStatus;
            deviceCardLayer.querySelector('.device-status-layer').classList.add(typeStatusColor);

        } else if (difference >= 60000) {
            let message_text = displayElapsedTime(difference).length > 8
                ? displayElapsedTime(difference).substring(0, 8)
                : displayElapsedTime(difference).padStart(8, ' ');
            deviceCard.querySelector('.device-status').textContent = message_text;
            deviceCard.querySelector('.device-status').classList.add("badge-danger");

            deviceCardLayer.querySelector('.device-status-layer').textContent = message_text;
            deviceCardLayer.querySelector('.device-status-layer').classList.add("badge-danger");

        }
    });
}

function displayElapsedTime(timeMilliseconds) {
    let timeSeconds = timeMilliseconds / 1000;
    let message;
    if (timeSeconds < 60) {
        message = timeSeconds.toFixed(0) + " sec";
    } else if (timeSeconds < 3600) {
        let timeMinutes = timeSeconds / 60;
        message = timeMinutes.toFixed(0) + " min";
    } else if (timeSeconds < 86400) {
        let timeHours = timeSeconds / 3600;
        message = timeHours.toFixed(0) + " hours";
    } else if (timeSeconds < 2678400) {
        let timeDays = timeSeconds / 86400;
        message = timeDays.toFixed(0) + " days";
    } else if (timeSeconds < 31536000) {
        let timeMonths = timeSeconds / 2628000;
        message = timeMonths.toFixed(0) + " month";
    } else {
        let timeYears = timeSeconds / 31536000;
        message = timeYears.toFixed(0) + " years";
    }
    return message;
}

function checkAndRemoveOldPositions() {
    for (let i = 0; i < polylineSegments.length; i++) {
        polylineSegments[i].setMap(null);
    }
    polylineSegments = [];
    arrayDevices.forEach(function (device) {
        let newPositions = [];
        device.positionsLog.forEach(function (position) {
            let date_format = position.date;
            let date_now = new Date().toISOString();
            let date_one = new Date(date_now);
            let date_two = new Date(date_format);
            let difference = Math.abs(date_one - date_two);
            if (difference <= 3600000) {
                let devicePosition = {
                    attributes: position.attributes,
                    date: position.date,
                    speed: position.speed,
                    lat: parseFloat(position.lat),
                    lng: parseFloat(position.lng),
                };
                newPositions.push(devicePosition);
            }
        });
        device.positionsLog = newPositions;
        if (device.positionsLog.length > 0) {
            createPolyline(newPositions);
        }
    });
}

function createPolyline(points) {
    for (let i = 1; i < points.length; i++) {
        let speed = points[i].speed;
        let color = getColorForSpeed(speed);
        let segment = new google.maps.Polyline({
            map: map,
            path: [
                new google.maps.LatLng(points[i - 1].lat, points[i - 1].lng),
                new google.maps.LatLng(points[i].lat, points[i].lng),
            ],
            strokeColor: color,
            strokeOpacity: 1.0,
            strokeWeight: 5,
        });
        (function (segmentIndex) {
            google.maps.event.addListener(segment, 'mouseover', function (e) {
                let point = points[segmentIndex];
                let dateFormat = formatDate(point.date);
                let event = point.attributes?.event ?? '';

                let content = `
                    <div class="col-lg-12 col-12">
                        <div>
                            <span class="text-sm">Speed:</span>
                            <span class="text-dark text-sm font-weight-bold">${point.speed} km/h</span>
                            <span class="text-sm ms-2">Date:</span>
                            <span class="text-dark font-weight-bold">${dateFormat}</span>
                        </div>
                        <div>
                            <span class="text-sm">Motion:</span>
                            <span class="text-dark text-sm font-weight-bold">${point.attributes.motion}</span>
                            <span class="text-sm ms-2">event:</span>
                            <span class="text-dark font-weight-bold">${event}</span>
                        </div>
                    </div>
                `;

                infoWindow.setContent(content);
                infoWindow.setPosition(e.latLng);
                infoWindow.open(map);
            });

            google.maps.event.addListener(segment, 'mouseout', function () {
                infoWindow.close();
            });
        })(i);
        polylineSegments.push(segment);
    }
    return polylineSegments;
}

function updateNewPolyline(points) {
    if (points.length > 1) {
        let penultimateValue = points[points.length - 2];
        let lastValue = points[points.length - 1];
        let combinedPoints = [penultimateValue].concat(lastValue);
        return createPolyline(combinedPoints);
    }
    return [];
}

function getColorForSpeed(speed) {
    var minSpeed = 0;
    var maxSpeed = 20;
    var percent = (speed - minSpeed) / (maxSpeed - minSpeed);
    var color_map = [
        "#301E6A",
        "#0B4C6B",
        "#007A5E",
        "#6BA143",
        "#B2B04E",
        "#F8FF78",
        "#FFA869",
        "#F15C5C",
    ];
    var index = Math.floor(percent * (color_map.length - 1));
    return color_map[index];
}

document.addEventListener('DOMContentLoaded', function () {
    setInterval(checkAliveDevices, 30000);
    setInterval(checkAndRemoveOldPositions, 10000);
    createTableGeofence();
    // createTable();
    // setInterval(checkAndRemoveOldPositions, 10000);
});





function initLayers() {
    layerAis = new google.maps.KmlLayer({
        url:
            "https://ais.emotiva.com.co/output/ais.kml?test=" +
            Math.round(new Date().getTime()),
        suppressInfoWindows: false,
        preserveViewport: true,
        map: map,
    });

    layerMile = new google.maps.KmlLayer({
        url:
            "https://impala.emotiva.com.co/milemarks.kml?test=" +
            Math.round(new Date().getTime()),
        suppressInfoWindows: false,
        preserveViewport: true,
        map: map,
    });

    layerIdeam = new google.maps.KmlLayer({
        url:
            "https://impala.emotiva.co/upgrade/gps/ideam.kml?test=" +
            Math.round(new Date().getTime()),
        suppressInfoWindows: false,
        preserveViewport: true,
        map: map,
    });

    layerEnc = new google.maps.KmlLayer({
        url:
            "https://impala.emotiva.co/upgrade/enc.kml?test=" +
            Math.round(new Date().getTime()),
        suppressInfoWindows: false,
        preserveViewport: true,
        map: map,
    });

    layerPointsAttention = new google.maps.KmlLayer({
        url:
            "https://impala.emotiva.com.co/upgrade/atencion.kml?test=" +
            Math.round(new Date().getTime()),
        suppressInfoWindows: false,
        preserveViewport: true,
        map: map,
    });

    layerCellularCoverage = new google.maps.KmlLayer({
        url:
            "https://impala.emotiva.com.co/upgrade/coverage.kml?test=" +
            Math.round(new Date().getTime()),
        suppressInfoWindows: false,
        preserveViewport: true,
        map: map,
    });

    layers.checkbox_ais = layerAis;
    layers.checkbox_mile = layerMile;
    layers.checkbox_ideam = layerIdeam;
    layers.checkbox_enc = layerEnc;
    layers.checkbox_points_attention = layerPointsAttention;
    layers.checkbox_cellular_coverage = layerCellularCoverage;

    layerAis.setMap(map);
    layerMile.setMap(map);
    layerIdeam.setMap(map);
    layerEnc.setMap(map);
    layerPointsAttention.setMap(map);
    layerCellularCoverage.setMap(map);
}

const checkboxes_layers = document.querySelectorAll('input[type="checkbox"]');
checkboxes_layers.forEach(checkbox => {
    checkbox.addEventListener('change', function () {
        const layer = layers[checkbox.id];
        if (layer) {
            layer.setMap(this.checked ? map : null);
        }
    });
});


/* *//////////////////////


var points_geofence = [];

function saveButton() {

    let selectedMode = document.getElementById('select-geofence').value;
    let area;
    if (selectedMode === 'POLYGON') {
        let polygon = points_geofence.map(point => `${point.lat} ${point.lng}`).join(', ');
        area = `POLYGON ((${polygon}))`;
    } else if (selectedMode === 'POLYLINE') {
        let lineString = points_geofence.map(point => `${point.lat} ${point.lng}`).join(', ');
        area = `LINESTRING (${lineString})`;
    }


    var formData = $("#formGeofence").serialize();
    formData += "&area=" + area;
    $.ajax({
        url: geofence_url,
        type: "POST",
        data: formData,
        success: function (response) {

            if (dataTableSearchtrack2) {
                dataTableSearchtrack2.destroy();
                dataTableSearchtrack2 = null;
            }
            document.getElementById('name_geofence').value = '';
            document.getElementById('name_description').value = '';
            createTableGeofence();


        },
    });

    points_geofence = [];
}



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
let polygonsGeofence = [];

function drawGeofence(response) {

    response.forEach(geocerca => {
        const path = wktToLatLngArray(geocerca.area);

        if (geocerca.area.startsWith('POLYGON')) {
            const polygon =new google.maps.Polygon({
                paths: path,
                strokeColor: '#FF0000',
                strokeOpacity: 0.8,
                strokeWeight: 2,
                fillColor: '#FF0000',
                fillOpacity: 0.15,
                map: map
            });
            polygonsGeofence.push(polygon);
        } else if (geocerca.area.startsWith('LINESTRING')) {
            const polygon =new google.maps.Polyline({
                path: path,
                strokeColor: '#0000FF',
                strokeOpacity: 0.8,
                strokeWeight: 2,
                map: map
            });
            polygonsGeofence.push(polygon);
        }
    });

}

function clearGeofences() {
    polygonsGeofence.forEach(polygon => {
        polygon.setMap(null);
    });
    polygonsGeofence = [];
}
