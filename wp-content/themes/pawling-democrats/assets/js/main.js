document.addEventListener('DOMContentLoaded', function () {
	var toggle = document.querySelector('.menu-toggle');
	var nav = document.getElementById('primary-navigation');

	if (!toggle || !nav) {
		return;
	}

	toggle.addEventListener('click', function () {
		var isOpen = nav.classList.toggle('is-open');
		toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
	});
});
