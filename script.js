// Product Management
function showSchPortal() {
    const portal = document.getElementById('schoolportal');
    if (portal) {
        portal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    } else {
        console.error('School portal element not found');
    }
}

function hideSchPortal() {
    const portal = document.getElementById('schoolportal');
    if (portal) {
        portal.classList.add('hidden');
        document.body.style.overflow = 'auto';
    }
}

function showProjPortal() {
    const portal = document.getElementById('projectportal');
    if (portal) {
        portal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    } else {
        console.error('Project portal element not found');
    }
}

function hideProjPortal() {
    const portal = document.getElementById('projectportal');
    if (portal) {
        portal.classList.add('hidden');
        document.body.style.overflow = 'auto';
    }
}

// Business Transformation
function showBusinessTransformPortal() {
    console.log('showBusinessTransformPortal called');
    const portal = document.getElementById('businesstransform');
    if (portal) {
        portal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    } else {
        console.error('Business Transformation portal element not found');
    }
}

function hideBusinessTransformPortal () {
    const portal = document.getElementById('businesstransform');
    if (portal) {
        portal.classList.add('hidden');
        document.body.style.overflow = 'auto';
    }
};

// Mobile Menu Toggle
function toggleMenu() {
    const nav = document.querySelector('.main-nav');
    const menuButton = document.querySelector('.menu-toggle i');
    if (nav && menuButton) {
        nav.classList.toggle('active');
        menuButton.classList.toggle('fa-bars');
        menuButton.classList.toggle('fa-times');
    }
}

// Navigation and Smooth Scrolling
document.addEventListener('DOMContentLoaded', () => {
    // Handle navigation clicks
    document.querySelectorAll('.main-nav a').forEach(link => {
        link.addEventListener('click', (e) => {
            // Skip for external links or links with onclick
            if (link.getAttribute('target') === '_blank' || link.getAttribute('onclick')) {
                return;
            }
            
            e.preventDefault();
            const sectionId = link.getAttribute('href').substring(1);
            const section = document.getElementById(sectionId);
            
            if (section) {
                // Remove active class from all links
                document.querySelectorAll('.main-nav a').forEach(navLink => {
                    navLink.classList.remove('active');
                });
                
                // Add active class to clicked link
                link.classList.add('active');
                
                // Smooth scroll to section
                section.scrollIntoView({ behavior: 'smooth' });
                
                // Close mobile menu if open
                const nav = document.querySelector('.main-nav');
                const menuButton = document.querySelector('.menu-toggle i');
                if (nav && nav.classList.contains('active') && menuButton) {
                    nav.classList.remove('active');
                    menuButton.classList.remove('fa-times');
                    menuButton.classList.add('fa-bars');
                }
            }
        });
    });

    // Form submission handler
    document.getElementById('contactForm').addEventListener('submit', function(event) {
        event.preventDefault();
        document.getElementById('submitBtn').disabled = true;

        const form = this;
        const formData = new FormData(form);
        const popupText = document.getElementById('popup-text');
        const popupMessage = document.getElementById('popup-message');

        // Validate email
        const email = formData.get('email');
        if (!email.match(/^[^\s@]+@[^\s@]+\.[^\s@]+$/)) {
            popupText.textContent = 'Error: Invalid email format';
            popupMessage.style.display = 'block';
            document.getElementById('submitBtn').disabled = false;
            return;
        }

        // Log form data
        console.log('Form data being sent:');
        for (let [key, value] of formData.entries()) {
            console.log(`${key}: ${value}`);
        }

        // Set up timeout
        const controller = new AbortController();
        const timeoutId = setTimeout(() => controller.abort(), 30000);

        // Step 1: Submit to submit.php
        // popupText.textContent = 'Saving data...';
        // popupMessage.style.display = 'block';

        fetch('submit.php', {
            method: 'POST',
            body: formData,
            signal: controller.signal
        })
        .then(response => {
            clearTimeout(timeoutId);
            if (!response.ok) {
                throw new Error(`Database HTTP error! Status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (!data.success) {
                throw new Error(data.message);
            }

            // Step 2: Submit to send_email.php
            // popupText.textContent = 'Sending email...';
            const emailController = new AbortController();
            const emailTimeoutId = setTimeout(() => emailController.abort(), 30000);

            return fetch('send_email.php', {
                method: 'POST',
                body: formData,
                signal: emailController.signal
            })
            .then(response => {
                clearTimeout(emailTimeoutId);
                if (!response.ok) {
                    throw new Error(`Email HTTP error! Status: ${response.status}`);
                }
                return response.json();
            })
            .then(emailData => {
                if (!emailData.success) {
                    throw new Error(emailData.message);
                }
                popupText.textContent = 'Your message has been saved and email sent successfully!';
                popupMessage.style.display = 'block';
                form.reset();
                document.querySelector('input[name="name"]').focus();
                document.getElementById('submitBtn').disabled = false;
            });
        })
        .catch(error => {
            clearTimeout(timeoutId);
            console.error('Fetch error:', error);
            // popupText.textContent = error.name === 'AbortError' ? 'Request timed out.' : `Error: ${error.message}`;
            // popupMessage.style.display = 'block';
            document.querySelector('input[name="name"]').focus();
            document.getElementById('submitBtn').disabled = false;
        });
    });
});

// Function to close the popup
function closePopup() {
    document.getElementById('popup-message').style.display = 'none';
}


// Expose functions globally for HTML inline onclick to work on mobile
window.showSchPortal = showSchPortal;
window.hideSchPortal = hideSchPortal;
window.showProjPortal = showProjPortal;
window.hideProjPortal = hideProjPortal;
window.showBusinessTransformPortal = showBusinessTransformPortal;
window.hideBusinessTransformPortal = hideBusinessTransformPortal;
