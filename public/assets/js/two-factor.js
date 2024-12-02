function toggleDiv(divToShow, divToHide) {
    const divShow = document.getElementById(divToShow);
    const divHide = document.getElementById(divToHide);

    if (divShow.style.display === "none") {
        divShow.style.display = "block";
        divHide.style.display = "none";
    } else {
        divShow.style.display = "none";
        divHide.style.display = "block";
    }
}