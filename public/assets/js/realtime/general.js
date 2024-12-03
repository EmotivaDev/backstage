function filterCards() {
    let input, filter, cards, cardContainer, title, i, txtValue;
    input = document.getElementById('searchBar');
    filter = input.value.toLowerCase();
    cardContainer = document.getElementById("deviceList");
    cards = cardContainer.getElementsByClassName('device-card');
    for (i = 0; i < cards.length; i++) {
        title = cards[i].textContent || cards[i].innerText;
        if (title.toLowerCase().indexOf(filter) > -1) {
            cards[i].style.display = "";
        } else {
            cards[i].style.display = "none";
        }
    }
}

const inputSearchBar = document.getElementById('searchBar');
const collapseElement = document.getElementById('collapseOne');

inputSearchBar.addEventListener('click', function () {
    if (inputSearchBar.value.trim() === "" && !collapseElement.classList.contains('show')) {
        $('#collapseOne').collapse();
    }else if(inputSearchBar.value.trim() === "" && collapseElement.classList.contains('show')){
        $('#collapseOne').collapse();
    }
});

function filterCardsLayer() {
   
    let input, filter, cards, cardContainer, title, i, txtValue;
    input = document.getElementById('searchBarLayer');
    filter = input.value.toLowerCase();
    cardContainer = document.getElementById("deviceListLayer");
    cards = cardContainer.getElementsByClassName('device-card-layer');
     console.log('ola')
    for (i = 0; i < cards.length; i++) {
        title = cards[i].textContent || cards[i].innerText;
        if (title.toLowerCase().indexOf(filter) > -1) {
            cards[i].style.display = "";
        } else {
            cards[i].style.display = "none";
        }
    }
}

document.addEventListener('DOMContentLoaded', function () {
    /* For Collapses Buttons Layers Right */
    const buttonsCollapse = document.querySelectorAll('.custom-collapse-toggle');
    buttonsCollapse.forEach(button => {
        button.addEventListener('click', function () {
            const targetCollapse = document.querySelector(this.getAttribute('data-bs-target'));
            new bootstrap.Collapse(targetCollapse, {
                toggle: true
            });
            const allCollapses = document.querySelectorAll('.collapse-buttons');
            allCollapses.forEach(collapse => {
                if (collapse !== targetCollapse && collapse.classList.contains('show')) {
                    new bootstrap.Collapse(collapse).hide();
                }
            });
        });
    });
});

function showOrHideNavEngines(device) {
    if (device.contact != null) {
        for (let i = 1; i <= 5; i++) {
            let engineId = 'E' + i.toString();
            if (device.contact.includes(engineId)) {
                document.getElementById('nav-engine' + i + '-tab').style.display = 'block';
            } else {
                document.getElementById('nav-engine' + i + '-tab').style.display = 'none';
            }
        }
        let engineDeviceId = device.contact[0].replace(/\D/g, '');
        document.getElementById('nav-engine' + engineDeviceId + '-tab').click();
    } else {
        for (let i = 1; i <= 5; i++) {
            let engineTab = document.getElementById(`nav-engine${i}-tab`);
            engineTab.style.display = "none";
        }
    }
}

function tripToast(message, status, icon, title) {
    let toastElement = null;
    let toast = null;
    if (!toastElement) {
        let toastContainer = document.createElement("div");
        toastContainer.className = "position-fixed top-1 end-1";
        toastContainer.style.zIndex = "9999";

        toastElement = document.createElement("div");
        toastElement.className = "toast fade p-2 bg-white";
        toastElement.role = "alert";
        toastElement.setAttribute("aria-live", "assertive");
        toastElement.id = "tripToast";
        toastElement.setAttribute("aria-atomic", "true");

        let toastHeader = document.createElement("div");
        toastHeader.className = "toast-header border-0";

        let Icon = document.createElement("i");
        Icon.className = "material-symbols-rounded text-" + status + " me-2";
        Icon.innerText = icon;

        let titleSpan = document.createElement("span");
        titleSpan.className = "me-auto font-weight-bold";
        titleSpan.innerText = status;

        let smallText = document.createElement("small");
        smallText.className = "text-body";
        smallText.innerText = ' Just Now';

        let closeIcon = document.createElement("i");
        closeIcon.className = "fas fa-times text-md ms-3 cursor-pointer";
        closeIcon.setAttribute("data-bs-dismiss", "toast");
        closeIcon.setAttribute("aria-label", "Close");
        closeIcon.setAttribute("aria-hidden", "true");

        let hrElement = document.createElement("hr");
        hrElement.className = "horizontal dark m-0";

        let toastBody = document.createElement("div");
        toastBody.className = "toast-body";

        toastHeader.appendChild(Icon);
        toastHeader.appendChild(titleSpan);
        toastHeader.appendChild(smallText);
        toastHeader.appendChild(closeIcon);

        toastElement.appendChild(toastHeader);
        toastElement.appendChild(hrElement);
        toastElement.appendChild(toastBody);

        toastContainer.appendChild(toastElement);
        document.body.appendChild(toastContainer);

        toast = new bootstrap.Toast(toastElement, {
            delay: 3000,
        });
    }

    let labelElement = document.createElement("label");
    labelElement.innerText = message;
    toastElement.querySelector(".toast-body").innerHTML = "";
    toastElement.querySelector(".toast-body").appendChild(labelElement);
    toastElement.querySelector("i").innerText = icon;
    toastElement.querySelector("i").className =
        "material-symbols-rounded text-" + status + " me-2";
    toastElement.querySelector("span").innerText = title;
    toast.show();
}
