$(document).ready(function () {
    $(".owl-carousel").owlCarousel({
        items: 1,
        loop: true,
        margin: 10,
        dots: false,
        mouseDrag: true,
        responsiveClass: true,
        autoplay: true,
        smartSpeed: 500,
        responsive: {
        0: {
            items: 1
        },
        480: {
            items: 1
        },
        767: {
            items: 1
        },
        993: {
            items: 1
        },
        1200: {
            items: 1
        }
        }
    })
});