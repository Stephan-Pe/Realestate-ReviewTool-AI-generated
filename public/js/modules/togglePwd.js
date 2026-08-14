export function togglePwd(input, toggle) {

    if (toggle && !toggle.classList.contains('listener-added')) {
        toggle.addEventListener("click", () => {
            // toggle the type attribute
            const type = input.getAttribute("type") === "password" ? "text" : "password";
            input.setAttribute("type", type);

            // toggle the icon
            toggle.classList.toggle("goodby-eye");
        });
        // Mark this toggle as having an event listener attached
        toggle.classList.add('listener-added');
    }

}


