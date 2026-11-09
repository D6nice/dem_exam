document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('input[name="date"]').forEach(function (input) {
        input.setAttribute('inputmode', 'numeric');
        input.setAttribute('maxlength', '10');

        input.addEventListener('input', function () {
            var digits = input.value.replace(/\D/g, '').slice(0, 8);
            var out = digits.slice(0, 2);
            if (digits.length > 2) out += '.' + digits.slice(2, 4);
            if (digits.length > 4) out += '.' + digits.slice(4, 8);
            input.value = out;
        });
    });

    document.querySelectorAll('input[name="time"]').forEach(function (input) {
        input.setAttribute('inputmode', 'numeric');
        input.setAttribute('maxlength', '5');

        input.addEventListener('input', function () {
            var digits = input.value.replace(/\D/g, '').slice(0, 4);
            var out = digits.slice(0, 2);
            if (digits.length > 2) out += ':' + digits.slice(2, 4);
            input.value = out;
        });
    });

    var form = document.querySelector('form[data-application-form]');
    if (form) {
        var dateInput = form.querySelector('input[name="date"]');
        var timeInput = form.querySelector('input[name="time"]');

        var setFieldError = function (input, message) {
            var field = input.closest('.field');
            if (!field) return;

            field.classList.toggle('has-error', Boolean(message));

            var box = field.querySelector('.error-text');
            if (message) {
                if (!box) {
                    box = document.createElement('div');
                    box.className = 'error-text';
                    field.appendChild(box);
                }
                box.textContent = message;
            } else if (box) {
                box.remove();
            }
        };

        var validDate = function (value) {
            var match = /^(\d{2})\.(\d{2})\.(\d{4})$/.exec(value);
            if (!match) return false;

            var day = parseInt(match[1], 10);
            var month = parseInt(match[2], 10);
            var year = parseInt(match[3], 10);
            var date = new Date(year, month - 1, day);

            return date.getFullYear() === year
                && date.getMonth() === month - 1
                && date.getDate() === day;
        };

        form.addEventListener('submit', function (event) {
            var ok = true;

            var date = dateInput.value.trim();
            if (!validDate(date)) {
                setFieldError(dateInput, 'Дата в формате ДД.ММ.ГГГГ');
                ok = false;
            } else {
                setFieldError(dateInput, null);
            }

            var time = timeInput.value.trim();
            if (!/^(?:[01]\d|2[0-3]):[0-5]\d$/.test(time)) {
                setFieldError(timeInput, 'Время в формате ЧЧ:ММ');
                ok = false;
            } else if (time < '09:00' || time > '20:00') {
                setFieldError(timeInput, 'Клиника работает с 09:00 до 20:00');
                ok = false;
            } else {
                setFieldError(timeInput, null);
            }

            if (!ok) {
                event.preventDefault();
            }
        });
    }

    var slider = document.getElementById('slider');
    if (slider) {
        var slides = Array.prototype.slice.call(slider.querySelectorAll('.slide'));
        var dotsBox = slider.querySelector('.slider-dots');
        var index = 0;
        var timer = null;

        slides.forEach(function (_, i) {
            var dot = document.createElement('button');
            dot.type = 'button';
            dot.className = 'slider-dot';
            dot.setAttribute('aria-label', 'Слайд ' + (i + 1));
            dot.addEventListener('click', function () {
                go(i);
                restart();
            });
            dotsBox.appendChild(dot);
        });

        var dots = Array.prototype.slice.call(dotsBox.children);

        function go(i) {
            index = (i + slides.length) % slides.length;

            slides.forEach(function (slide, n) {
                slide.classList.toggle('active', n === index);
            });

            dots.forEach(function (dot, n) {
                dot.classList.toggle('active', n === index);
            });
        }

        function start() {
            stop();
            timer = setInterval(function () {
                go(index + 1);
            }, 3000);
        }

        function stop() {
            if (timer) {
                clearInterval(timer);
                timer = null;
            }
        }

        function restart() {
            stop();
            start();
        }

        var prev = slider.querySelector('.slider-prev');
        var next = slider.querySelector('.slider-next');

        if (prev) {
            prev.addEventListener('click', function () {
                go(index - 1);
                restart();
            });
        }

        if (next) {
            next.addEventListener('click', function () {
                go(index + 1);
                restart();
            });
        }

        slider.addEventListener('mouseenter', stop);
        slider.addEventListener('mouseleave', start);

        go(0);
        start();
    }

    document.querySelectorAll('#toasts .toast').forEach(function (toast) {
        requestAnimationFrame(function () {
            toast.classList.add('show');
        });

        setTimeout(function () {
            toast.classList.remove('show');
            setTimeout(function () {
                toast.remove();
            }, 300);
        }, 4000);
    });
});
