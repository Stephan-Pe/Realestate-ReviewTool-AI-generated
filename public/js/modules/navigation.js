export const hamburger = document.querySelector(".nav__menu");
const myBody = document.querySelector("body");
const navigation = document.querySelector(".nav__offscreen");

let timeoutID;
export function mobileNavigation() {
    clearTimeout(timeoutID);
    hamburger.classList.toggle('active');
    navigation.classList.toggle("open");
    myBody.classList.toggle("no-scroll");
    hamburger.setAttribute('aria-expanded', 'true');
    timeoutID = setTimeout(() => {
        hamburger.classList.remove('active');
        navigation.classList.remove("open");
        myBody.classList.remove("no-scroll");
        hamburger.setAttribute('aria-expanded', 'false');
    }, 4500);

}
