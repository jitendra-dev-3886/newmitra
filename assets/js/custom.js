/*jquery js use for marquee start*/
marqueeInit({
	uniqueid: 'LogoSlider',
	inc: 5, //speed - pixel increment for each iteration of this marquee's movement
	mouse: 'cursor driven', //mouseover behavior ('pause' 'cursor driven' or false)
	moveatleast: 2,
	neutral: 150,
	savedirection: true,
	random: true
});
/*jquery js use for marquee end*/

/*this js use for banner slider {start here}*/
var swiper = new Swiper('.bannerSlider', {
pagination: '.swiper-pagination',
paginationClickable: true,
nextButton: '.swiper-button-next',
prevButton: '.swiper-button-prev',
loop: 'true',
autoplay: 2500,
spaceBetween:0
});
/*this js use for banner slider {end here}*/