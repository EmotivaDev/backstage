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
    connectWebsockets();
}

let arrayDevices = [];
let markers = [];
let polylineSegments = [];
let map, infoWindow, markerSelected;

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
                                trip: trip
                            };

                        });
                        addMarkers(arrayDevices);
                        writeDevicesList(arrayDevices);
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
                        /*  Open Test  */
                        position.attributes = {
                            "priority": 1,
                            "sat": 17,
                            "event": 109,
                            "ignition": true,
                            "motion": true,
                            "rssi": 5,
                            "io200": 0,
                            "io69": 1,
                            "in1": false,
                            "in2": false,
                            "io113": 99,
                            "pdop": 1.1,
                            "hdop": 0.6000000000000001,
                            "power": 12.098,
                            "io24": 7,
                            "io206": 30086,
                            "battery": 3.991,
                            "io68": 0,
                            "operator": 732101,
                            "odometer": 263069,
                            "io636": 3881266,
                            "io109": "26398/14027/26816/1568/1475/1577/77/78/77/68/66/0/0/0/0/22/0/0/68/77/69/206/0/0/69/65/57/26/26/26/0.00/0.00/0.00/133205.14/200797.18/231117.60/0/0/0/0/0/0/0/0/0/12/1/13/0.00/42.61/81.83/0.00",
                            "emi": "{\"tfuel1\":133205.14,\"vol2\":26,\"vol1\":26,\"efp3\":0,\"efp2\":0,\"efp1\":22,\"tfuel2\":200797.18,\"tfuel3\":231117.6,\"ofuel3\":0,\"rpm3\":1577,\"ofuel1\":0,\"ofuel2\":0,\"rpm2\":1475,\"rpm1\":1568,\"load3\":0,\"load2\":0,\"load1\":0,\"sfuel2\":1,\"sfuel1\":12,\"sfuel3\":13,\"eop3\":69,\"eop2\":77,\"eop1\":68,\"eot3\":0,\"eot2\":66,\"hours2\":14027,\"eot1\":68,\"top3\":0,\"tfuelecu3\":0,\"hours1\":26398,\"top1\":206,\"tfuelecu1\":0,\"top2\":0,\"tfuelecu2\":0,\"tot3\":57,\"tot1\":69,\"hours3\":26816,\"tot2\":65,\"rfuel1\":0,\"rfuel2\":42.61,\"rfuel3\":81.83,\"ect3\":77,\"ifuel1\":0,\"ect2\":78,\"ifuel2\":0,\"depth\":0,\"ect1\":77,\"ifuel3\":0,\"vol3\":26,\"cfuel3\":0,\"cfuel2\":0,\"cfuel1\":0}",
                            "ip": "191.156.245.175",
                            "distance": 63.087605597567794,
                            "totalDistance": 263798.42671892175,
                            "hours": 233131000
                        }
                        /*  Closed Test  */
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
        for (const key in position) {
            if (position.hasOwnProperty(key)) {
                let closestPosition = findClosestPosition(position.latitude, position.longitude);
                device[key] = position[key];
                device['trip'] = JSON.parse(position.trip);
                device['course'] = position.course + courseFormatter(position.course);
                device['speed'] = parseFloat((position.speed * 1.852).toFixed(2));
                device['location'] = closestPosition.location ? closestPosition.location.toUpperCase() : null;
                device['milemark'] = closestPosition.milemark ?? null;
                device['description'] = closestPosition.description ?? null;


            }
        }
        let deviceSelected = getSelectedDevice();

        updateMarker(device);

        if (device.deviceId == deviceSelected.deviceId) {
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
        let device = getSelectedDevice();
        let enginesNav = document.getElementById('nav_engines');
        showOrHideNavEngines(device);
        if (tabContentId == "nav-telemetry-tab") {
            enginesNav.style.display = "block";
            updateTelemetry(device);
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
        let device = getSelectedDevice();
        updateTelemetry(device);
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

function getSelectedDevice() {
    const selectedDeviceCard = document.querySelector(".device-card.selected");

    if (selectedDeviceCard) {
        let deviceId = selectedDeviceCard.getAttribute("data-id");
        let device = arrayDevices.find(d => d.deviceId === parseInt(deviceId));
        return device;
    } else {
        return null;
    }
}

// click in the list Device
document.addEventListener("deviceSelectionChanged", function (event) {
    let device = getSelectedDevice();
    updateText("title_device_selected", device.name.toUpperCase().substring(0, 15));
    showOrHideNavEngines(device);
    updateNavigation(device);
    updateTelemetry(device);
    showTrip();

    var marker = markers[device.deviceId];
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

        const deviceNumber = document.createElement("div");
        deviceNumber.classList.add("device-number");
        deviceNumber.textContent = device.name.toUpperCase().substring(0, 15);

        const deviceDetails = document.createElement("div");
        deviceDetails.classList.add("d-flex");

        const deviceCode = document.createElement("span");
        deviceCode.classList.add("device-code");

        const deviceModel = document.createElement("span");
        deviceModel.classList.add("device-model");
        deviceModel.textContent = device.speed + " km/h" || "Model A";

        const userCount = document.createElement("span");
        userCount.classList.add("user-count");

        deviceDetails.append(deviceCode, deviceModel, userCount);
        deviceInfo.append(deviceNumber, deviceDetails);

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
                detail: {
                    deviceId: device.deviceId,
                    name: device.name,
                    model: device.model || "Model A",
                    code: deviceCode.textContent,
                    location: location.textContent
                }
            });
            document.dispatchEvent(changeEvent);
        });
        
    });

    checkAliveDevices();
    if (devices.length > 0) {
        const firstDeviceCard = deviceListElement.querySelector(".device-card");
        if (firstDeviceCard) {
            firstDeviceCard.click();
        }
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
    let device = getSelectedDevice();

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
    let device = getSelectedDevice();
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
    let device = getSelectedDevice();

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
    let device = getSelectedDevice();
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
    let device = getSelectedDevice();

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
    let device = getSelectedDevice();
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
        let locationTelemetry = device.location + "-" + device.milemark + (device.description ? " - " + device.description : "");
        document.querySelector(`[data-id="${device.deviceId}"] .device-location`).textContent = locationTelemetry;

        let tripStatus = device.trip && device.trip.number !== null ? "Trip" : null;
        let tripStatusHidden = device.trip && device.trip.number !== null ? false : true;
        document.querySelector(`[data-id="${device.deviceId}"] .device-code`).textContent = tripStatus;
        document.querySelector(`[data-id="${device.deviceId}"] .device-code`).hidden = tripStatusHidden;

        let dateFormat = formatDate(device.deviceTime);
        let fixedDate = new Date(dateFormat);
        let currentDate = new Date();
        let difference = Math.abs(currentDate - fixedDate);
        document.querySelector(`[data-id="${device.deviceId}"] .device-model`).classList.remove("badge-warning", "badge-success", "badge-danger");
        if (!device.deviceTime) {
            document.querySelector(`[data-id="${device.deviceId}"] .device-model`).textContent = 'Offline';
            document.querySelector(`[data-id="${device.deviceId}"] .device-model`).classList.add("badge-danger");
        } else if (difference < 60000) {
            let typeStatusColor = device.speed === 0 ? "badge-warning" : "badge-success";
            let typeStatus = device.speed === 0 ? "Stopped" : "Moving";
            document.querySelector(`[data-id="${device.deviceId}"] .device-model`).textContent = typeStatus;
            document.querySelector(`[data-id="${device.deviceId}"] .device-model`).classList.add(typeStatusColor);
        } else if (difference >= 60000) {
            let message_text = displayElapsedTime(difference).length > 8
                ? displayElapsedTime(difference).substring(0, 8)
                : displayElapsedTime(difference).padStart(8, ' ');
            document.querySelector(`[data-id="${device.deviceId}"] .device-model`).textContent = message_text;
            document.querySelector(`[data-id="${device.deviceId}"] .device-model`).classList.add("badge-danger");
        }
    });
}

function displayElapsedTime(timeMilliseconds) {
    var timeSeconds = timeMilliseconds / 1000;
    var message;
    if (timeSeconds < 60) {
        message = timeSeconds.toFixed(0) + " sec";
    } else if (timeSeconds < 3600) {
        var timeMinutes = timeSeconds / 60;
        message = timeMinutes.toFixed(0) + " min";
    } else if (timeSeconds < 86400) {
        var timeHours = timeSeconds / 3600;
        message = timeHours.toFixed(0) + " hours";
    } else if (timeSeconds < 2678400) {
        var timeDays = timeSeconds / 86400;
        message = timeDays.toFixed(0) + " days";
    } else if (timeSeconds < 31536000) {
        var timeMonths = timeSeconds / 2628000;
        message = timeMonths.toFixed(0) + " month";
    } else {
        var timeYears = timeSeconds / 31536000;
        message = timeYears.toFixed(0) + " years";
    }
    return message;
}

function checkAndRemoveOldPositions() {
    for (var i = 0; i < polylineSegments.length; i++) {
        polylineSegments[i].setMap(null);
    }
    polylineSegments = [];
    arrayDevices.forEach(function (device) {
        var newPositions = [];
        device.positions.forEach(function (position) {
            var date_format = position.date;
            var date_now = new Date().toISOString();
            var date_one = new Date(date_now);
            var date_two = new Date(date_format);
            var difference = Math.abs(date_one - date_two);
            if (difference <= 3600000) {
                var devicePosition = {
                    attributes: position.attributes,
                    date: position.date,
                    speed: position.speed,
                    lat: parseFloat(position.lat),
                    lng: parseFloat(position.lng),
                };
                newPositions.push(devicePosition);
            }
        });
        device.positions = newPositions;
        if (device.positions.length > 0) {
            createPolyline(newPositions);
        }
    });
}


document.addEventListener('DOMContentLoaded', function () {
    setInterval(checkAliveDevices, 10000);
    // setInterval(checkAndRemoveOldPositions, 10000);
});


