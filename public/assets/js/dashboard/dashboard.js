// This section manages the fleet positions
const dateSelectFleetPosition = flatpickr("#date_select_fleet_position", {
    allowInput: true,
    enableTime: true,
    dateFormat: "Y-m-d H:i:S",
    defaultDate: new Date(),
});

let tableDeviceFleetPosition;

const dateStartElement = document.getElementById("date_select_fleet_position");
dateStartElement.addEventListener("change", function () {
    const date = this.value;
    getFleetPosition(date);
});

const fleet_position = document.getElementById("fleet_position");
fleet_position.addEventListener("click", function () {
    dateSelectFleetPosition.setDate(new Date());
    getFleetPosition(dateStartElement.value);
});

function getFleetPosition(date) {
    fetch("/statistics/fleet_position/" + date)
        .then((response) => response.json())
        .then((data) => {
            console.log(data);
            document.getElementById("fleet_position_motion").textContent =
                data.statistics_fleet_position.totalActive;
            document.getElementById("fleet_position_rest").textContent =
                data.statistics_fleet_position.totalStops;
            document.getElementById("fleet_position_avg_speed").textContent =
                data.statistics_fleet_position.avgSpeed + " km/h";

            if (tableDeviceFleetPosition) {
                tableDeviceFleetPosition.destroy();
            }

            let newRows = data.devices.map((device) => {
                const imageUrl = "/assets/images/boat.png";
                let speed = parseFloat((device.speed * 1.852).toFixed(2));
                let course = courseFormatter(device.course);
                let description = device.location.description ? " " + device.location.description : '';
                let location = device.location ? device.location.location.toUpperCase() + " - " + device.location.milemark + description : '-';
                return [
                    `<div class="d-flex px-3 py-1">
                        <div>
                            <img src="${imageUrl}" class="avatar-fleet me-3" alt="image">
                        </div>
                        <div class="d-flex flex-column justify-content-center">
                            <h6 class="mb-0 font-weight-bold">${device.name.toUpperCase()}</h6>
                            <p class="text-sm font-weight-normal text-secondary mb-0">
                                <span class="text-info font-weight-bold">${device.devicetime}</span>
                            </p>
                        </div>
                    </div>`,
                    `<p class="text-sm font-weight-normal mb-0">${speed} km/h</p>`,
                    `<p class="text-sm font-weight-normal mb-0">${device.course + course
                    }</p>`,
                    `<p class="text-sm font-weight-normal mb-0">${device.altitude} m</p>`,
                    `${location}`,
                ];
            });

            const tableDeviceFleetPositionElement = document.getElementById("table_device_fleet_position");

            tableDeviceFleetPosition = new simpleDatatables.DataTable(
                tableDeviceFleetPositionElement,
                {
                    data: {
                        headings: [
                            "Vessel",
                            "Speed",
                            "Course",
                            "Altitude",
                            "Location",
                        ],
                        data: newRows,
                    },
                    searchable: true,
                    fixedHeight: true,
                }
            );
        })
        .catch((error) => console.error("Error fetching data:", error));
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