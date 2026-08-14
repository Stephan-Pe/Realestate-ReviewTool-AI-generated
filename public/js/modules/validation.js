import { flashMessage, errorMsg } from "./alert.js";

export const queryForm = document.getElementById('queryForm');
const queryInput = document.getElementById('queryPosts');

// search if there is searchparam
function required(input) {
  if (input.value.length == 0) {
    flashMessage('Please enter search params');
    return false;
  }

  return true;
}

export function logSubmit(evt) {
  if (required(queryInput)) {
    return false;
  }
  evt.preventDefault();

}
// regex check parameters
const reg_name = /([\<\>\#\(\)])/g;

const reg_email = /[a-zA-Z0-9._&'*+-]+@[a-zA-Z0-9 &-]+\.[a-z]{2,}/;

const reg_password = /^(?=.{8,})(?=.*[a-z])(?=.*[A-Z])(?=.*[£@#$%^&+=]).*/;



const registerForm = document.registerform;
const loginForm = document.userlogin;


const submitSignup = document.getElementById('submitSignup');
const submitLogin = document.getElementById('submitLogin');


function send(e) {
  e.preventDefault();
  // on click event will be forced
  if (registerForm) {
    // good to go

    registerForm.submit();
  }
  if (loginForm) {
    loginForm.submit();
  }

}


export const init = function () {

  if (submitSignup && validateForm(registerForm) && checkEmptyInputs()) {
    submitSignup.addEventListener("click", send);
  }

  if (submitLogin && validateForm(loginForm) && checkEmptyInputs()) {
    submitLogin.addEventListener("click", send);
  }
};

function checkEmptyInputs() {
  let inputs = document.querySelectorAll('input');
  inputs.forEach((element, index) => {
    element.addEventListener('input', e => {
      if (e.target.hasAttribute('required') && e.target.value === '') {
        errorMsg(e.target, 'Feld muss ausgefüllt werden!');
        return false;
      }
      return true;
    })


  });
}


function validateForm() {
  // checkEmptyInputs();

  let inputs = document.querySelectorAll('input');
  inputs.forEach((element, index) => {
    element.addEventListener('change', (e) => {

      if (e.target.name === 'user_name' && e.target.value === '') {
        errorMsg(e.target, 'Feld muss ausgefüllt werden!');
        return false;
      }
      if (e.target.name === 'user_name' && reg_name.test(e.target.value)) {
        errorMsg(e.target, 'Unerlaubte Sonderzeichen');
        return false;
      }

      if (e.target.name === 'user_email' && !reg_email.test(e.target.value)) {
        errorMsg(e.target, 'Bitte valide Emailadresse eingeben');
        return false;
      }
      if (e.target.name === 'user_password' && !reg_password.test(e.target.value)) {
        errorMsg(e.target, 'Passwort muss aus mindestens 8 Zeichen, gross- und klein, inklusive Sonderzeichen Ziffern bestehen!');
        return false;
      }
      return true;
    })


  });

}












