/**
 * DineSpot — client-side interactions
 * Loaded on every page via footer.php. Runs after DOMContentLoaded.
 */
document.addEventListener('DOMContentLoaded', function () {
    var navToggle = document.querySelector('.nav-toggle');
    var primaryNav = document.getElementById('primary-nav');
    var successMessage = document.querySelector('.alert-success');

    // Auto-dismiss success alerts after 5 seconds
    if (successMessage) {
        setTimeout(function () {
            successMessage.style.display = 'none';
        }, 5000);
    }

    // Mobile navigation toggle (hamburger menu)
    if (navToggle && primaryNav) {
        navToggle.addEventListener('click', function () {
            var isOpen = primaryNav.classList.toggle('is-open');
            navToggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        });
    }

    // FAQ accordion — only one item open at a time
    document.querySelectorAll('.faq-question').forEach(function (button) {
        button.addEventListener('click', function () {
            var item = button.closest('.faq-item');
            var isOpen = item.classList.contains('is-open');

            document.querySelectorAll('.faq-item').forEach(function (faqItem) {
                faqItem.classList.remove('is-open');
                faqItem.querySelector('.faq-question').setAttribute('aria-expanded', 'false');
            });

            if (!isOpen) {
                item.classList.add('is-open');
                button.setAttribute('aria-expanded', 'true');
            }
        });
    });

    // Restaurant menu tabs on the detail page
    var menuTabs = document.querySelectorAll('[data-menu-tab]');
    var menuPanels = document.querySelectorAll('[data-menu-panel]');

    menuTabs.forEach(function (tab, index) {
        tab.addEventListener('click', function () {
            menuTabs.forEach(function (item, tabIndex) {
                var active = tabIndex === index;
                item.classList.toggle('is-active', active);
                item.setAttribute('aria-selected', active ? 'true' : 'false');
            });

            menuPanels.forEach(function (panel, panelIndex) {
                panel.classList.toggle('is-active', panelIndex === index);
            });
        });
    });

    // Leaflet map on restaurant detail page (requires Leaflet loaded in page head)
    var mapElement = document.getElementById('restaurant-map');

    if (mapElement && typeof L !== 'undefined') {
        var lat = parseFloat(mapElement.dataset.lat);
        var lng = parseFloat(mapElement.dataset.lng);
        var name = mapElement.dataset.name || 'Restaurant';

        var map = L.map(mapElement).setView([lat, lng], 14);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        L.marker([lat, lng]).addTo(map).bindPopup(name);
    }

    // Quick guide stepper (guide.php)
    var guidePanels = document.querySelectorAll('[data-guide-panel]');
    var guideTriggers = document.querySelectorAll('[data-guide-trigger]');
    var guideSteps = document.querySelectorAll('[data-guide-step]');
    var guidePrev = document.getElementById('guide-prev');
    var guideNext = document.getElementById('guide-next');
    var activeGuideStep = 0;

    function showGuideStep(index) {
        if (!guidePanels.length) {
            return;
        }

        activeGuideStep = index;
        guidePanels.forEach(function (panel, panelIndex) {
            var active = panelIndex === index;
            panel.classList.toggle('is-active', active);
            panel.hidden = !active;
        });
        guideSteps.forEach(function (step, stepIndex) {
            var active = stepIndex === index;
            step.classList.toggle('is-active', active);
            var button = step.querySelector('[data-guide-trigger]');
            if (button) {
                button.setAttribute('aria-current', active ? 'step' : 'false');
            }
        });
        if (guidePrev) {
            guidePrev.disabled = index === 0;
        }
        if (index === guidePanels.length - 1) {
            guideNext.textContent = 'Finish';
            guideNext.addEventListener('click', function () {
                window.location.href = 'index.php';
            });
        }
    }

    guideTriggers.forEach(function (button) {
        button.addEventListener('click', function () {
            showGuideStep(parseInt(button.getAttribute('data-guide-trigger'), 10));
        });
    });

    if (guidePrev) {
        guidePrev.addEventListener('click', function () {
            if (activeGuideStep > 0) {
                showGuideStep(activeGuideStep - 1);
            }
        });
    }

    if (guideNext) {
        guideNext.addEventListener('click', function () {
            if (activeGuideStep < guidePanels.length - 1) {
                showGuideStep(activeGuideStep + 1);
            }
        });
    }

    // Reservation modal — live deposit & demand estimate
    function formatCurrency(amount) {
        return '$' + amount.toFixed(2);
    }

    var reservationDate = document.getElementById('reservation_date');
    var reservationTime = document.getElementById('reservation_time');
    var partySizeSelect = document.getElementById('party_size');
    var reservationOccasion = document.getElementById('reservation_occasion');
    var reservationDeposit = document.querySelector('[data-reservation-deposit]');
    var reservationDemand = document.querySelector('[data-reservation-demand]');
    var reservationNote = document.querySelector('[data-reservation-note]');

    function updateReservationEstimate() {
        if (!partySizeSelect || !reservationDeposit || !reservationDemand || !reservationNote) {
            return;
        }

        var partySize = parseInt(partySizeSelect.value, 10) || 1;
        var deposit = 0;
        var demand = 'Standard';
        var notes = [];

        if (partySize >= 9) {
            deposit = 50;
            notes.push('Large parties may require extra confirmation from the restaurant.');
        } else if (partySize >= 5) {
            deposit = 20;
            notes.push('Medium-sized parties may require a small hold deposit.');
        } else {
            notes.push('No deposit is estimated for small parties.');
        }

        if (reservationDate && reservationDate.value) {
            var selectedDate = new Date(reservationDate.value + 'T00:00:00');
            var isWeekend = selectedDate.getDay() === 0 || selectedDate.getDay() === 6;
            if (isWeekend) {
                demand = 'High';
                notes.push('Weekend reservations are usually busier.');
            }
        }

        if (reservationTime && reservationTime.value) {
            var hour = parseInt(reservationTime.value.split(':')[0], 10);
            if (hour >= 18 && hour <= 20) {
                demand = 'High';
                notes.push('Dinner rush time may have limited availability.');
            }
        }

        if (reservationOccasion && reservationOccasion.value !== '') {
            notes.push('Add any celebration details in Special Requests.');
        }

        reservationDeposit.textContent = formatCurrency(deposit);
        reservationDemand.textContent = demand;
        reservationNote.textContent = notes.join(' ');
    }

    [reservationDate, reservationTime, partySizeSelect, reservationOccasion].forEach(function (field) {
        if (field) {
            field.addEventListener('input', updateReservationEstimate);
            field.addEventListener('change', updateReservationEstimate);
        }
    });
    updateReservationEstimate();

    // Meal cost calculator on restaurant detail page (13% tax)
    var mealCalculator = document.querySelector(".meal-calculator");

    if (mealCalculator) {
        var mealCalculatorItems = mealCalculator.querySelectorAll('[data-meal-item]');
        var mealCalculatorTipPercent = mealCalculator.querySelector('[data-tip-percent]');
        var mealCalculatorSubtotalOutput = mealCalculator.querySelector('[data-meal-subtotal]');
        var mealCalculatorTaxOutput = mealCalculator.querySelector('[data-meal-tax]');
        var mealCalculatorTipOutput = mealCalculator.querySelector('[data-meal-tip]');
        var mealCalculatorTotalOutput = mealCalculator.querySelector('[data-meal-total]');
        
        function updateMealTotal() {
            var subtotal = 0;
            var selectedTip = mealCalculatorTipPercent ? parseFloat(mealCalculatorTipPercent.value) : 0;
            
            mealCalculatorItems.forEach(function (item) {
                if (item.checked) {
                    subtotal += parseFloat(item.value) || 0;
                }
            });
            
            var tax = subtotal * 0.13;
            var tip = subtotal * (selectedTip / 100);
            var total = subtotal + tax + tip;
            
            mealCalculatorSubtotalOutput.textContent = formatCurrency(subtotal);
            mealCalculatorTaxOutput.textContent = formatCurrency(tax);
            mealCalculatorTipOutput.textContent = formatCurrency(tip);
            mealCalculatorTotalOutput.textContent = formatCurrency(total);
        }

        mealCalculatorItems.forEach(function (item) {
            item.addEventListener('change', updateMealTotal);
        });
        if (mealCalculatorTipPercent) {
            mealCalculatorTipPercent.addEventListener('change', updateMealTotal);
        }
        updateMealTotal();
    }

    // Reusable confirmation modal for cancel/delete actions
    var confirmModal = document.getElementById('confirm-modal');
    var confirmModalForm = document.getElementById('confirm-modal-form');
    var confirmModalTitle = document.getElementById('confirm-modal-title');
    var confirmModalMessage = document.getElementById('confirm-modal-message');
    var confirmModalId = document.getElementById('confirm-modal-id');
    var confirmModalRedirect = document.getElementById('confirm-modal-redirect');
    var confirmModalSubmit = document.getElementById('confirm-modal-submit');
    var confirmModalCancel = document.getElementById('confirm-modal-cancel');

    function openConfirmModal(config) {
        if (!confirmModal || !confirmModalForm) {
            return;
        }

        confirmModalTitle.textContent = config.title;
        confirmModalMessage.textContent = config.message;
        confirmModalId.value = config.id;
        confirmModalRedirect.value = config.redirect;
        confirmModalForm.action = config.action;
        confirmModalSubmit.textContent = config.submitLabel;
        confirmModal.hidden = false;
        document.body.classList.add('modal-open');
        confirmModalSubmit.focus();
    }

    function closeConfirmModal() {
        if (!confirmModal) {
            return;
        }

        confirmModal.hidden = true;
        document.body.classList.remove('modal-open');
    }

    document.querySelectorAll('[data-cancel-reservation]').forEach(function (button) {
        button.addEventListener('click', function () {
            if (button.dataset.status == 'approved') {
                document.querySelector('[data-cancel-reservation-message]').textContent = 'You have an approved reservation at ' + button.dataset.restaurantName + '. You cannot cancel it by yourself. Please call us at (519) 555-0142.';
                document.querySelector('[data-cancel-reservation-message]').style.display = 'block';
                document.querySelector('[data-cancel-reservation-message]').style.color = 'red';
                document.querySelector('#cancel-reservation-button').style.display = 'none';
                return;
            }
            openConfirmModal({
                title: 'Cancel Reservation?',
                message: 'Cancel your reservation at ' + button.dataset.restaurantName + ' on '
                    + button.dataset.reservationDate + ' at ' + button.dataset.reservationTime
                    + ' for ' + button.dataset.partySize + ' guests?',
                id: button.dataset.reservationId,
                redirect: button.dataset.redirect,
                action: button.dataset.formAction,
                submitLabel: 'Confirm Cancellation'
            });
        });
    });

    document.querySelectorAll('[data-delete-review]').forEach(function (button) {
        button.addEventListener('click', function () {
            openConfirmModal({
                title: 'Delete Review?',
                message: 'Delete your review for ' + button.dataset.restaurantName + '? This cannot be undone.',
                id: button.dataset.reviewId,
                redirect: button.dataset.redirect,
                action: button.dataset.formAction,
                submitLabel: 'Delete Review'
            });
        });
    });

    if (confirmModalCancel) {
        confirmModalCancel.addEventListener('click', closeConfirmModal);
    }

    if (confirmModal) {
        confirmModal.addEventListener('click', function (event) {
            if (event.target === confirmModal) {
                closeConfirmModal();
            }
        });
    }

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape' && confirmModal && !confirmModal.hidden) {
            closeConfirmModal();
        }
    });

    // Chart.js visualisations on the Insights page (data injected via window.dineSpotCharts)
    if (window.dineSpotCharts && typeof Chart !== 'undefined') {
        var cuisineCanvas = document.getElementById('cuisine-chart');
        var reservationCanvas = document.getElementById('reservation-chart');

        if (cuisineCanvas) {
            new Chart(cuisineCanvas, {
                type: 'bar',
                data: {
                    labels: window.dineSpotCharts.cuisines.labels,
                    datasets: [{
                        label: 'Restaurants',
                        data: window.dineSpotCharts.cuisines.values,
                        backgroundColor: '#8b2942'
                    }]
                },
                options: {
                    responsive: true,
                    plugins: { legend: { display: false } },
                    scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }
                }
            });
        }

        if (reservationCanvas) {
            new Chart(reservationCanvas, {
                type: 'doughnut',
                data: {
                    labels: window.dineSpotCharts.reservations.labels,
                    datasets: [{
                        data: window.dineSpotCharts.reservations.values,
                        backgroundColor: ['#8b2942', '#c9a962', '#2d6a4f', '#9b2226', '#5c534c']
                    }]
                },
                options: {
                    responsive: true
                }
            });
        }
    }

    // Handle forgot password
    var forgotPassword = document.querySelector('.forgot-password');
    var forgotPasswordMessage = document.querySelector('.forgot-password-message');
    if (forgotPassword) {
        forgotPassword.addEventListener('click', function () {
            forgotPasswordMessage.textContent = 'Forgot password? Please email us at support@dinespot.com or call us at (519) 555-0142';
            forgotPasswordMessage.style.display = 'block';
            forgotPasswordMessage.style.color = 'red';
            forgotPasswordMessage.style.fontSize = '14px';
            forgotPasswordMessage.style.fontWeight = 'bold';
            forgotPasswordMessage.style.textAlign = 'center';
            forgotPasswordMessage.style.margin = '10px 0';
        });
    }
});
