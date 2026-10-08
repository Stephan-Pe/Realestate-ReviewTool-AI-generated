/**
 * Extract CSRF token from the Twig template form element
 */
function getCsrfToken() {
  const tokenInput = document.querySelector('input[name="csrf_token"]');
  // console.log("CSRF Token:", tokenInput ? tokenInput.value : "Not found");
  return tokenInput ? tokenInput.value : "";
}

/**
 * Check if the cookie consent decision already exists in client storage
 */
export function hasConsent() {
  return document.cookie
    .split("; ")
    .some(row => row.startsWith("cookie_consent="));
}

/**
 * Toggle visibility of the banner overlay
 */
export function toggleOverlay(show) {
  const overlay = document.getElementById("cookieOverlay");
  if (overlay) {
    overlay.style.display = show ? "flex" : "none";
    document.body.style.overflow = show ? "hidden" : "";
  }
}

/**
 * Send choice to backend to validate CSRF and attach Set-Cookie header
 */
async function saveConsent(consentPayload) {
  console.log('CONSENT FETCH START');
  const csrf = getCsrfToken();

  try {
    const response = await fetch("/cookies/consent", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        "X-CSRF-TOKEN": csrf
      },
      body: JSON.stringify({ ...consentPayload, csrf_token: csrf })
    });
console.log('CONSENT FETCH RESPONSE', response.status, response.url);
    if (!response.ok) {
      throw new Error(`Server returned ${response.status}`);
    }

    // Hide overlay upon successful server response
    toggleOverlay(false);

    // Notify client-side scripts (e.g., Google Analytics initializer)
    document.dispatchEvent(
      new CustomEvent("cookieConsentUpdated", {
        detail: consentPayload
      })
    );
  } catch (error) {
    console.error("Failed to save cookie consent:", error);
  }
}

/**
 * Initialize banner and bind user action listeners
 */
export function initCookieConsent() {
  const overlay = document.getElementById("cookieOverlay");
  if (!overlay) return;
  // Check if consent already exists in client cookies
  if (hasConsent()) {
    // Hide/remove the banner immediately if SSR rendered it by mistake
    overlay.style.display = "none";
    return;
  }
  // Step 2: Show the banner for first-time visitors
  toggleOverlay(true);
  // Step 3: Bind Accept button
  const acceptBtn = document.getElementById("cookieAcceptBtn");
  if (acceptBtn && !acceptBtn.dataset.bound) {
    acceptBtn.addEventListener("click", () => {
      saveConsent({ analytics: false, marketing: false, necessary: true });
      acceptBtn.dataset.bound = "true"; // Prevent duplicate listeners
    });
  }

  // Step 4: Close when clicking background outside container
  overlay.addEventListener("click", e => {
    if (e.target === overlay) {
      saveConsent({ analytics: false, marketing: false, necessary: true });
    }
  });
}
