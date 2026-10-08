import { imageSlider } from "./modules/imageSlider.js";
import { hamburger, mobileNavigation } from './modules/navigation.js';
import { moveFiles } from "./modules/fileUpload.js";
import { showToTopBtn } from "./modules/scrollTop.js";
import { showImpressum } from "./modules/impressum.js";
import { observer, btnObserver } from "./modules/observer.js";
import { displayLoading, hideLoading } from './modules/loader.js';
import { togglePwd } from './modules/togglePwd.js';
import { init } from "./modules/validation.js";
import { showMap } from "./modules/showMap.js";
import { init as initReviewTool } from "./modules/reviewTool.js";
import { initCookieConsent } from "./modules/cookieConsent.js";

const container = document.querySelector('.container');
const baseURI = window.location.origin + '/';
const currentLocation = window.location.href;
const homePageAnchor = document.getElementById('home');
const toggleLogin = document.querySelector('[data-toggleLogin]');
const inputLogin = document.querySelector('[data-passwordLogin]');
const toggleSignup = document.querySelector('[data-toggleSignup]');
const inputSignup = document.querySelector('[data-passwordSignup]');
const galleryWrapper = document.querySelector('[data-carousel]');

const saleInput = document.querySelector('[data-newSalesFile]');
const galleryInput = document.querySelector('[data-newGalleryFile]');
const galleryEditInput = document.querySelector('[data-editGalleryFile]');

const contactForm = document.querySelector('#contactForm');
const slideBtn = document.querySelectorAll('.slide-Btn');
// disable for dev mode - enable for production
const isDev =
  location.hostname === "localhost" ||
  location.hostname === "127.0.0.1" ||
  location.hostname.endsWith(".immo.test") ||
  location.hostname === "immo.test";

window.addEventListener('DOMContentLoaded', () => {
    init();
    initReviewTool();
});
// Call the function when the DOM is ready
document.addEventListener("DOMContentLoaded", initCookieConsent);

window.addEventListener("load", (event) => {

  displayLoading();
  setTimeout(() => {
    hideLoading();
  }, 250);
  contactForm?.addEventListener('submit', function (e) {

    e.preventDefault();
    // confirm('Vielen Dank für Ihr Interesse!');
    contactForm.submit();
  })

});
document.addEventListener("DOMContentLoaded", (event) => {
  if (container) {
    showImpressum();
    showMap();
  }


});

window.addEventListener('load', () => {
  let itemsToObserve = document.querySelectorAll('.parallax__lead');
  itemsToObserve.forEach(item => {
    observer.observe(item);
  });
  if (slideBtn) {
    slideBtn.forEach(btn => {
      btnObserver.observe(btn)
    });
  }

  if (galleryWrapper) {
    imageSlider();
  }
  if (saleInput) {
    moveFiles(saleInput);
  }
  if (galleryInput) {
    moveFiles(galleryInput);
  }
  if (galleryEditInput) {
    moveFiles(galleryEditInput);
  }
  if (inputLogin) togglePwd(inputLogin, toggleLogin);
  if (inputSignup) togglePwd(inputSignup, toggleSignup);
  // accessability page current
  if (currentLocation === baseURI) {
    homePageAnchor.setAttribute('aria-current', 'page');
  }
  // mobile navigation
  if (hamburger) {
    hamburger.addEventListener('click', mobileNavigation);
    hamburger.addEventListener('keydown', (e) => {
      if (e.isComposing || e.keyCode !== 13) {
        return;
      }
      mobileNavigation();
    });

  }

});

// toTopButton
let lastKnownScrollPosition = 0;
let ticking = false;
/* eslint-disable-next-line */
window.addEventListener('scroll', function (e) {
  lastKnownScrollPosition = window.scrollY;
  if (!ticking) {
    window.requestAnimationFrame(function () {
      showToTopBtn(lastKnownScrollPosition);
      ticking = false;
    });

    ticking = true;
  }
});

const registerServiceWorker = async () => {
  if ("serviceWorker" in navigator) {
    try {
      const registration = await navigator.serviceWorker.register("/sw.js", {
        scope: "/"
      });
      if (isDev) {
        console.log(
          "Service worker registered with scope:",
          registration.scope
        );
      } else {
        console.log("Have a great day! :)");
      }
    } catch (error) {
      if (isDev) {
        console.error("Service worker registration failed:", error);
      }
    }
  }
};