// Main JavaScript file for Property Platform
$(document).ready(function() {
    // Mobile navigation toggle
    $('.nav-toggle').click(function() {
        $(this).toggleClass('active');
        $('.nav-menu, .nav-auth').toggleClass('mobile-show');
    });
    
    // Search form functionality
    $('#searchForm').on('submit', function(e) {
        e.preventDefault();
        performSearch();
    });
    
    // Real-time search functionality
    let searchTimeout;
    $('.search-input').on('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(performSearch, 500);
    });
    
    // Property filtering
    $('.filter-btn').click(function() {
        const filterType = $(this).data('filter');
        const filterValue = $(this).data('value');
        applyFilter(filterType, filterValue);
        
        // Update active state
        $(this).siblings().removeClass('active');
        $(this).addClass('active');
    });
    
    // Favorite functionality
    $('.favorite-btn').click(function(e) {
        e.preventDefault();
        const listingId = $(this).data('listing-id');
        toggleFavorite(listingId, $(this));
    });
    
    // Image gallery functionality
    if ($('.property-gallery').length) {
        initializeGallery();
    }
    
    // Form validation
    $('form').on('submit', function(e) {
        if (!validateForm($(this))) {
            e.preventDefault();
        }
    });
    
    // File upload preview
    $('.file-input').change(function() {
        previewImages(this);
    });
    
    // Smooth scrolling
    $('a[href^="#"]').click(function(e) {
        e.preventDefault();
        const target = $($(this).attr('href'));
        if (target.length) {
            $('html, body').animate({
                scrollTop: target.offset().top - 100
            }, 500);
        }
    });
    
    // Auto-hide alerts
    $('.alert').delay(5000).fadeOut();
    
    // Initialize tooltips if any
    initializeTooltips();
});

// Search functionality
function performSearch() {
    const formData = new FormData($('#searchForm')[0]);
    const params = new URLSearchParams(formData);
    
    // Show loading state
    $('#searchResults').html('<div class="text-center"><div class="spinner"></div> Searching...</div>');
    
    // Perform AJAX search
    $.get('/property-platform/api/search.php?' + params.toString())
        .done(function(data) {
            $('#searchResults').html(data);
        })
        .fail(function() {
            $('#searchResults').html('<div class="alert alert-error">Search failed. Please try again.</div>');
        });
}

// Filter functionality
function applyFilter(filterType, filterValue) {
    const currentUrl = new URL(window.location);
    currentUrl.searchParams.set(filterType, filterValue);
    window.location.href = currentUrl.toString();
}

// Favorite toggle functionality
function toggleFavorite(listingId, button) {
    // Check if user is logged in
    if (!isLoggedIn()) {
        window.location.href = '/property-platform/auth/login.php';
        return;
    }
    
    const isFavorited = button.hasClass('favorited');
    const action = isFavorited ? 'remove' : 'add';
    
    $.post('/property-platform/api/favorites.php', {
        action: action,
        listing_id: listingId
    })
    .done(function(response) {
        if (response.success) {
            button.toggleClass('favorited');
            const icon = button.find('i');
            icon.toggleClass('fas far');
            
            // Update button text
            const text = button.find('.btn-text');
            if (text.length) {
                text.text(isFavorited ? 'Add to Favorites' : 'Remove from Favorites');
            }
            
            showNotification(response.message, 'success');
        } else {
            showNotification(response.message, 'error');
        }
    })
    .fail(function() {
        showNotification('Failed to update favorites. Please try again.', 'error');
    });
}

// Form validation
function validateForm(form) {
    let isValid = true;
    
    // Clear previous errors
    form.find('.error-message').remove();
    form.find('.form-control').removeClass('error');
    
    // Validate required fields
    form.find('[required]').each(function() {
        const field = $(this);
        const value = field.val().trim();
        
        if (!value) {
            showFieldError(field, 'This field is required');
            isValid = false;
        }
    });
    
    // Validate email fields
    form.find('input[type="email"]').each(function() {
        const field = $(this);
        const value = field.val().trim();
        
        if (value && !isValidEmail(value)) {
            showFieldError(field, 'Please enter a valid email address');
            isValid = false;
        }
    });
    
    // Validate password confirmation
    const password = form.find('input[name="password"]').val();
    const confirmPassword = form.find('input[name="confirm_password"]').val();
    
    if (password && confirmPassword && password !== confirmPassword) {
        showFieldError(form.find('input[name="confirm_password"]'), 'Passwords do not match');
        isValid = false;
    }
    
    return isValid;
}

function showFieldError(field, message) {
    field.addClass('error');
    field.after('<div class="error-message" style="color: var(--danger-color); font-size: 0.875rem; margin-top: 0.25rem;">' + message + '</div>');
}

function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

// Image preview functionality
function previewImages(input) {
    const previewContainer = $(input).siblings('.image-preview');
    if (!previewContainer.length) {
        $(input).after('<div class="image-preview"></div>');
    }
    
    previewContainer.empty();
    
    if (input.files) {
        Array.from(input.files).forEach(function(file, index) {
            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const imageHtml = `
                        <div class="preview-item">
                            <img src="${e.target.result}" alt="Preview">
                            <button type="button" class="remove-image" data-index="${index}">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    `;
                    previewContainer.append(imageHtml);
                };
                reader.readAsDataURL(file);
            }
        });
    }
}

// Remove image from preview
$(document).on('click', '.remove-image', function() {
    const index = $(this).data('index');
    $(this).parent().remove();
    // Note: Actual file removal would require more complex handling
});

// Gallery initialization
function initializeGallery() {
    $('.gallery-thumbnail').click(function() {
        const newSrc = $(this).attr('src');
        const newAlt = $(this).attr('alt');
        
        $('.gallery-main img').attr('src', newSrc).attr('alt', newAlt);
        
        // Update active thumbnail
        $('.gallery-thumbnail').removeClass('active');
        $(this).addClass('active');
    });
}

// Notification system
function showNotification(message, type = 'info') {
    const notificationHtml = `
        <div class="notification alert alert-${type}" style="position: fixed; top: 20px; right: 20px; z-index: 1000; min-width: 300px;">
            ${message}
            <button type="button" class="close-notification" style="float: right; background: none; border: none; font-size: 1.2rem; cursor: pointer;">&times;</button>
        </div>
    `;
    
    $('body').append(notificationHtml);
    
    // Auto-hide after 5 seconds
    setTimeout(function() {
        $('.notification').fadeOut(function() {
            $(this).remove();
        });
    }, 5000);
}

// Close notification manually
$(document).on('click', '.close-notification', function() {
    $(this).parent().fadeOut(function() {
        $(this).remove();
    });
});

// Check if user is logged in (you might want to set this server-side)
function isLoggedIn() {
    // This would typically be set by PHP in a script tag
    return typeof window.userLoggedIn !== 'undefined' && window.userLoggedIn;
}

// Initialize tooltips
function initializeTooltips() {
    $('[data-tooltip]').hover(
        function() {
            const tooltip = $(this).data('tooltip');
            $(this).append(`<div class="tooltip">${tooltip}</div>`);
        },
        function() {
            $(this).find('.tooltip').remove();
        }
    );
}

// Price formatting
function formatPrice(price) {
    return '$' + parseFloat(price).toLocaleString('en-US', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
}

// Lazy loading for images
function initializeLazyLoading() {
    if ('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.dataset.src;
                    img.classList.remove('lazy');
                    imageObserver.unobserve(img);
                }
            });
        });
        
        document.querySelectorAll('img[data-src]').forEach(img => {
            imageObserver.observe(img);
        });
    }
}

// Initialize on page load
$(document).ready(function() {
    initializeLazyLoading();
});