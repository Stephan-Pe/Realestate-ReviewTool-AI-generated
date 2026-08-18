/**
 * Cookie Consent Module
 * Handles the cookie consent overlay and cookie storage
 */

const COOKIE_NAME = 'cookie_consent';
const COOKIE_EXPIRY = 31536000; // 1 year in seconds

// ===================================================================
//  CSRF Token (same pattern as reviewTool.js)
// ===================================================================

/**
 * Get CSRF token from the hidden input in the form
 * @returns {string}
 */
function getCsrfToken() {
    const tokenInput = document.querySelector('input[name="csrf_token"]');
    return tokenInput ? tokenInput.value : '';
}

/**
 * Check if cookie consent has been given
 */
export function hasConsent() {
    const cookie = getCookie(COOKIE_NAME);
    return cookie !== null;
}

/**
 * Get the current cookie consent state
 * Returns default state if no consent exists
 */
export function getConsentState() {
    const cookie = getCookie(COOKIE_NAME);
    if (cookie) {
        try {
            return JSON.parse(cookie);
        } catch (e) {
            console.error('Failed to parse cookie_consent:', e);
        }
    }
    return {
        analytics: false,
        marketing: false,
        necessary: true
    };
}

/**
 * Show the cookie consent overlay
 */
export function showConsentOverlay() {
    const overlay = document.getElementById('cookieOverlay');
    if (overlay) {
        overlay.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }
}

/**
 * Hide the cookie consent overlay
 */
export function hideConsentOverlay() {
    const overlay = document.getElementById('cookieOverlay');
    if (overlay) {
        overlay.style.display = 'none';
        document.body.style.overflow = '';
    }
}

/**
 * Set the cookie_consent cookie
 */
function setConsentCookie(consent) {
    const expires = new Date();
    expires.setTime(expires.getTime() + COOKIE_EXPIRY * 1000);

    document.cookie = `${COOKIE_NAME}=${JSON.stringify(consent)};expires=${expires.toUTCString()};path=/;Secure;samesite=Lax`;
}

/**
 * Get a cookie by name
 */
function getCookie(name) {
    const value = `; ${document.cookie}`;
    const parts = value.split(`; ${name}=`);
    if (parts.length === 2) {
        return parts.pop().split(';').shift();
    }
    return null;
}

/**
 * Handle accepting all cookies (or user's selection)
 */
export async function acceptCookies() {
    const analytics = document.getElementById('cookieAnalytics')?.checked || false;
    const marketing = document.getElementById('cookieMarketing')?.checked || false;

    const consent = {
        analytics: Boolean(analytics),
        marketing: Boolean(marketing),
        necessary: true
    };

    setConsentCookie(consent);
    hideConsentOverlay();

    try {
        const csrf = getCsrfToken();
        const response = await fetch('/cookies/consent', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrf,
            },
            body: JSON.stringify({ ...consent, csrf_token: csrf })
        });

        if (!response.ok) {
            const text = await response.text();
            console.warn('Server-side cookie consent update failed:', response.status, response.statusText, text?.slice(0, 200));
            return;
        }

        const data = await response.json();
        console.log('Cookie consent saved:', data);
    } catch (error) {
        console.error('Error saving cookie consent:', error);
    }

    // Dispatch custom event for other modules to listen to
    document.dispatchEvent(new CustomEvent('cookieConsentGiven', {
        detail: consent
    }));
}

/**
 * Handle rejecting non-essential cookies
 */
export async function rejectCookies() {
    const consent = {
        analytics: false,
        marketing: false,
        necessary: true
    };

    setConsentCookie(consent);
    hideConsentOverlay();

    try {
        const csrf = getCsrfToken();
        const response = await fetch('/cookies/reject', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrf,
            },
            body: JSON.stringify({ ...consent, csrf_token: csrf })
        });

        if (!response.ok) {
            const text = await response.text();
            console.warn('Server-side cookie rejection failed:', response.status, response.statusText, text?.slice(0, 200));
            return;
        }

        const data = await response.json();
        console.log('Cookie rejection saved:', data);
    } catch (error) {
        console.error('Error rejecting cookies:', error);
    }

    document.dispatchEvent(new CustomEvent('cookieConsentGiven', {
        detail: consent
    }));
}

/**
 * Initialize the cookie consent module
 * Shows the overlay only if consent hasn't been given yet
 */
export function initCookieConsent() {
    if (hasConsent()) {
        return; // Consent already given, do nothing
    }

    const overlay = document.getElementById('cookieOverlay');
    if (!overlay) {
        console.warn('Cookie consent overlay not found in DOM');
        return;
    }

    // Bind accept button
    const acceptBtn = document.getElementById('cookieAcceptBtn');
    if (acceptBtn) {
        acceptBtn.addEventListener('click', acceptCookies);
    }

    // Bind reject button
    const rejectBtn = document.getElementById('cookieRejectBtn');
    if (rejectBtn) {
        rejectBtn.addEventListener('click', rejectCookies);
    }

    // Bind close button
    const closeBtn = document.getElementById('cookieCloseBtn');
    if (closeBtn) {
        closeBtn.addEventListener('click', rejectCookies);
    }

    // Close on overlay background click
    overlay.addEventListener('click', (e) => {
        if (e.target === overlay) {
            rejectCookies();
        }
    });

    // Show the overlay
    showConsentOverlay();
}
