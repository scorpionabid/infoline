// Mobile device detection
const MobileDetect = {
    isMobile: () => window.innerWidth <= 768,
    isTouch: () => 'ontouchstart' in window || navigator.maxTouchPoints > 0,
    
    init() {
        this.updateBodyClass();
        window.addEventListener('resize', () => this.updateBodyClass());
    },
    
    updateBodyClass() {
        document.body.classList.toggle('is-mobile', this.isMobile());
        document.body.classList.toggle('is-touch', this.isTouch());
    }
};

// Mobile-specific UI adjustments
export const MobileUI = {
    init() {
        MobileDetect.init();
        this.setupMobileMenu();
        this.setupMobileTableView();
        this.setupMobileModals();
        this.setupPullToRefresh();
    },

    setupMobileMenu() {
        // Close menu when clicking outside
        $(document).on('click', (e) => {
            if (MobileDetect.isMobile() && $('.navbar-collapse').hasClass('show')) {
                if (!$(e.target).closest('.navbar').length) {
                    $('.navbar-collapse').collapse('hide');
                }
            }
        });

        // Add swipe support for mobile menu
        let touchStartX = 0;
        document.addEventListener('touchstart', e => {
            touchStartX = e.changedTouches[0].screenX;
        }, { passive: true });

        document.addEventListener('touchend', e => {
            const touchEndX = e.changedTouches[0].screenX;
            const diff = touchStartX - touchEndX;

            if (Math.abs(diff) > 50) {
                if (diff > 0) {
                    // Swipe left - close menu
                    $('.navbar-collapse').collapse('hide');
                } else {
                    // Swipe right - open menu
                    $('.navbar-collapse').collapse('show');
                }
            }
        }, { passive: true });
    },

    setupMobileTableView() {
        if (MobileDetect.isMobile()) {
            // Convert tables to mobile-friendly view
            $('.table:not(.no-mobile-convert)').each(function() {
                const headers = [];
                $(this).find('th').each(function() {
                    headers.push($(this).text());
                });

                $(this).find('tbody tr').each(function() {
                    const $row = $(this);
                    $row.find('td').each(function(i) {
                        $(this).attr('data-label', headers[i]);
                    });
                });
            });

            // Add horizontal scroll indicators
            $('.table-responsive').each(function() {
                if (this.scrollWidth > this.clientWidth) {
                    $(this).addClass('has-scroll');
                }
            });
        }
    },

    setupMobileModals() {
        if (MobileDetect.isMobile()) {
            // Adjust modal position for mobile
            $('.modal').on('show.bs.modal', function() {
                $(this).addClass('mobile-modal');
            });

            // Add swipe down to close for modals
            $('.modal').each(function() {
                let touchStartY = 0;
                const modal = $(this);

                modal.on('touchstart', '.modal-content', function(e) {
                    touchStartY = e.originalEvent.touches[0].clientY;
                });

                modal.on('touchmove', '.modal-content', function(e) {
                    const touchY = e.originalEvent.touches[0].clientY;
                    const diff = touchY - touchStartY;

                    if (diff > 50) {
                        modal.modal('hide');
                    }
                });
            });
        }
    },

    setupPullToRefresh() {
        if (MobileDetect.isMobile()) {
            let touchStartY = 0;
            let touchStartScrollTop = 0;

            document.addEventListener('touchstart', e => {
                touchStartY = e.touches[0].clientY;
                touchStartScrollTop = document.documentElement.scrollTop;
            }, { passive: true });

            document.addEventListener('touchmove', e => {
                const touchY = e.touches[0].clientY;
                const diff = touchY - touchStartY;

                if (document.documentElement.scrollTop === 0 && diff > 70) {
                    e.preventDefault();
                    showNotification('Yenilənir...', 'info');
                    location.reload();
                }
            }, { passive: false });
        }
    }
};