
function getColorForSpeed(speed) {
    let minSpeed = 0;
    let maxSpeed = 20;
    let percent = (speed - minSpeed) / (maxSpeed - minSpeed);
    let color_map = [
        "#301E6A",
        "#0B4C6B",
        "#007A5E",
        "#6BA143",
        "#B2B04E",
        "#F8FF78",
        "#FFA869",
        "#F15C5C",
    ];
    let index = Math.floor(percent * (color_map.length - 1));
    return color_map[index];
}

let polylineSegments = [];
let marker;
let map;
let nameDevice;
let resume;
let files;
let array;
let selectDevices = document.getElementById("select-devices");
let selectFiles = document.getElementById("select-files");

let slider = document.getElementById("sliderRegular");
noUiSlider.create(slider, {
    start: 0,
    step: 1,
    connect: [true, false],
    range: {
        min: 0,
        max: 100
    }
});

let handle = slider.querySelector('.noUi-handle');

flatpickr('#date_start', {
    allowInput: true,
    enableTime: true,
    dateFormat: "Y-m-d H:i:S",
    defaultDate: "today"
});
flatpickr('#date_end', {
    allowInput: true,
    enableTime: true,
    dateFormat: "Y-m-d H:i:S",
    defaultDate: new Date()
});

function initMap() {
    map = new google.maps.Map(document.getElementById("map-history"), {
        center: {
            lat: 8.9373066,
            lng: -74.4946016,
        },
        zoom: 14,
        styles: (mapStyles = [{
            featureType: "water",
            stylers: [{
                color: "#6eb9fb"
            }],
        },]),
        mapTypeControl: false,
        fullscreenControl: false,
    });
}

function showLoading() {
    document.getElementById('loading').style.display = 'block';
}

function hideLoading() {
    document.getElementById('loading').style.display = 'none';
}

selectFiles.addEventListener("change", function () {
    if (files) {
        let selectedFile = selectFiles.value;

        if (selectedFile === "rose_point_track") {
            fileUrl = files.rosePointTrack;
        } else if (selectedFile === "open_cpn_track") {
            fileUrl = files.openCpnTrack;
        } else if (selectedFile === "kml_track") {
            fileUrl = files.kmlTrack;
        } else if (selectedFile === "excel_csv") {
            fileUrl = files.excelCsv;
        } else {
            fileUrl = '';
        }

        if (fileUrl) {
            downloadFile(fileUrl)
        }
    }
});

async function downloadFile(fileName) {
    try {
        let response = await fetch("history/download/" + fileName, {
            method: "GET",
            headers: {
                'Accept': 'application/json',
            }
        });

        if (!response.ok) {
            throw new Error('Network response was not ok ' + response.statusText);
        }

        let blob = await response.blob();

        let link = document.createElement('a');
        let url = URL.createObjectURL(blob);
        link.href = url;
        link.download = fileName;
        link.click();

        URL.revokeObjectURL(url);

    } catch (error) {
        console.error('Error downloading the file:', error);
    }
}

async function fetchDeviceData() {
    showLoading();

    if (marker != null) {
        slider.noUiSlider.set(0);
        marker.setMap(null);
        for (let i = 0; i < polylineSegments.length; i++) {
            polylineSegments[i].setMap(null);
        }
    }
    let params = {
        date_start: document.getElementById("date_start").value,
        date_end: document.getElementById("date_end").value,
        device: selectDevices.value
    };

    try {
        showLoading();
        let response = await fetch("history/device?" + new URLSearchParams(params), {
            method: "GET"
        });

        if (!response.ok) {
            throw new Error('Network response was not ok ' + response.statusText);
        }

        let responseData = await response.json();

        array = responseData.data;
        resume = responseData.resume;
        files = responseData.files;
        nameDevice = responseData.resume.name.toUpperCase();

        document.getElementById("name").textContent = nameDevice;

        if (array.length !== 0) {
            document.getElementById("travel_milemark").textContent = (resume.location ?? "-").toUpperCase();
            document.getElementById("travel_distance").textContent = `${resume.travelDistance} KM`;
            document.getElementById("total_time").textContent = resume.totalTime;
            document.getElementById("avg_cog").textContent = `${resume.avgCog} Deg`;
            document.getElementById("avg_speed").textContent = `${resume.avgSpeed} Km/h`;

            document.getElementById("avg_rpm").textContent = `${resume.avgEngineRpm.rpm1} - ${resume.avgEngineRpm.rpm2} - ${resume.avgEngineRpm.rpm3}`;

            document.getElementById("max_min_ect").textContent = `${resume.minMaxEngineEct.ect1Max}/${resume.minMaxEngineEct.ect1Min} - 
                                                                     ${resume.minMaxEngineEct.ect2Max}/${resume.minMaxEngineEct.ect2Min} - 
                                                                     ${resume.minMaxEngineEct.ect3Max}/${resume.minMaxEngineEct.ect3Min}`;

            document.getElementById("max_min_eop").textContent = `${resume.minMaxEngineEop.eop1Max}/${resume.minMaxEngineEop.eop1Min} - 
                                                                      ${resume.minMaxEngineEop.eop2Max}/${resume.minMaxEngineEop.eop2Min} - 
                                                                      ${resume.minMaxEngineEop.eop3Max}/${resume.minMaxEngineEop.eop3Min}`;

            let latlng = new google.maps.LatLng(array[0].latitude, array[0].longitude);

            // Create initial marker
            marker = new google.maps.Marker({
                position: {
                    lat: array[0].latitude,
                    lng: array[0].longitude,
                },
                map: map,
                icon: {
                    path: google.maps.SymbolPath.FORWARD_CLOSED_ARROW,
                    scale: 5,
                    rotation: array[0].course,
                    strokeColor: "gold",
                },
                label: {
                    text: `${nameDevice} - ${parseFloat((array[0].speed * 1.852).toFixed(2))} km/h - ${array[0].location}`,
                    fontWeight: 'bold',
                    fontSize: '12px',
                    fontFamily: '"Courier New", Courier,Monospace',
                    color: 'black',
                }
            });

            // Create polyline segments
            polylineSegments = [];
            for (let i = 1; i < array.length; i++) {
                let speed = parseFloat((array[i].speed * 1.852).toFixed(2));
                let color = getColorForSpeed(speed);
                let segment = new google.maps.Polyline({
                    map: map,
                    path: [
                        new google.maps.LatLng(array[i - 1].latitude, array[i - 1].longitude),
                        new google.maps.LatLng(array[i].latitude, array[i].longitude),
                    ],
                    strokeColor: color,
                    strokeOpacity: 1.0,
                    strokeWeight: 5,
                });
                polylineSegments.push(segment);
            }

            // Update the slider with the appropriate range
            slider.noUiSlider.updateOptions({
                range: {
                    'min': 0,
                    'max': array.length - 1,
                }
            });

            // Add keyboard event listener to handle slider updates
            handle.addEventListener('keydown', function (e) {
                let value = Number(slider.noUiSlider.get());
                if (e.which === 37) {
                    slider.noUiSlider.set(value - 1);
                }
                if (e.which === 39) {
                    slider.noUiSlider.set(value + 1);
                }
            });

            // Update the marker and other information when the slider changes
            slider.noUiSlider.on('update', function (values, handle) {
                try {
                    let index = parseInt(values[handle]);
                    let currentData = array[index];
                    let latlng = new google.maps.LatLng(currentData.latitude, currentData.longitude);

                    marker.setPosition(latlng);
                    marker.setIcon({
                        path: google.maps.SymbolPath.FORWARD_CLOSED_ARROW,
                        scale: 5,
                        rotation: currentData.course,
                        strokeColor: "gold",
                    });

                    let location = currentData.location ? currentData.location.toUpperCase() : '-';
                    marker.setLabel({
                        text: `${nameDevice} - ${parseFloat(currentData.speed * 1.852).toFixed(2)} km/h - ${location}`,
                        fontWeight: 'bold',
                        fontSize: '12px',
                        fontFamily: '"Courier New", Courier,Monospace',
                        color: 'black',
                    });

                    map.setCenter(latlng);

                    // Update info fields based on current data
                    document.getElementById("info_one").textContent = currentData.devicetime;
                    document.getElementById("info_three").textContent = location;
                    document.getElementById("info_two").textContent = `${(currentData.speed * 1.852).toFixed(2)} Km/h`;

                    // Handle additional engine data (if available)
                    let depth = 0, rpm1 = 0, rpm2 = 0, rpm3 = 0, ect1 = 0, ect2 = 0, ect3 = 0, eop1 = 0, eop2 = 0, eop3 = 0;

                    if (currentData.emi != null) {

                        depth = currentData.emi.depth ?? 0;
                        rpm1 = currentData.emi.rpm1 ?? 0;
                        rpm2 = currentData.emi.rpm2 ?? 0;
                        rpm3 = currentData.emi.rpm3 ?? 0;

                        ect1 = currentData.emi.ect1 ?? 0;
                        ect2 = currentData.emi.ect2 ?? 0;
                        ect3 = currentData.emi.ect3 ?? 0;

                        eop1 = currentData.emi.eop1 ?? 0;
                        eop2 = currentData.emi.eop2 ?? 0;
                        eop3 = currentData.emi.eop3 ?? 0;
                    }

                    document.getElementById("info_four").textContent = depth;
                    document.getElementById("info_five").textContent = `${rpm1} - ${rpm2} - ${rpm3}`;
                    document.getElementById("info_six").textContent = `${ect1} - ${ect2} - ${ect3}`;
                    document.getElementById("info_seven").textContent = `${eop1} - ${eop2} - ${eop3}`;

                } catch (e) {
                    console.error(e);
                }
            });

            map.setCenter(latlng);
        }

    } catch (error) {
        console.error("Error:", error);
    } finally {
        // Hide loading indicator
        hideLoading();
    }

}
