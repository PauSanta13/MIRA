function initSliderCarousel() {

    $('.component.slider-carousel .content-slider').slick({
        focusOnSelect: true,
        arrows: true,
        slidesToShow: 1,
        swipeToSlide: true,
        infinite: true,
        dots: true,
        responsive: [
            {
                breakpoint: 768,
                settings: {
                    infinite: true,
                }
            },
        ]

    });

};