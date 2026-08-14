import { flashMessage } from "./alert.js";
import { scrollToTop } from "./scrollTop.js";


export function moveFiles(inputElement) {

    // Listen to the change event on the <input> element
    inputElement.addEventListener('change', (event) => {
        // Get the selected image file
        const imageFiles = event.target.files;
        const avatar = document.querySelector('.drop__zone--prompt');
        // Empty the images div
        avatar.innerHTML = '';

        if (imageFiles.length > 0) {
            let allowedExt = /(\jpg|\jpeg|\png|\webp)$/i;
            // Loop through all the selected images
            for (const imageFile of imageFiles) {
                let extName = imageFile.name;
                if (imageFile.size >= 500000) {
                    scrollToTop();
                    flashMessage(`${imageFile.name} ist zu gross! 500kb max!`);
                    return false;
                } else if (!allowedExt.exec(extName)) {
                    scrollToTop();
                    flashMessage('Dateityp nicht unterstützt!');
                    return false;
                } else {
                    const reader = new FileReader();

                    // Convert each image file to a string
                    reader.readAsDataURL(imageFile);

                    // FileReader will emit the load event when the data URL is ready
                    // Access the string using reader.result inside the callback function
                    reader.addEventListener('load', () => {

                        // Create new <img> element and add it to the DOM
                        avatar.innerHTML += `
                <div class="drop__zone--thumb">
                    <img src='${reader.result}'>
                    <span class='drop__zone--name'>${imageFile.name}</span>
                </div>
            `;
                    });
                }


            }
        } else {
            // Empty the images div
            avatar.innerHTML = '';
        }
    });
};


export function handleVideoUpload() {
    const errorOutput = document.getElementById('videoError');
    const videoInput = document.getElementById('fileSelect');
    const videoFile = videoInput.files[0]; /* now you can work with the file list */
    const fileSize = videoFile ? videoFile['size'] : undefined;
    const fileName = videoFile ? videoFile['name'] : undefined;
    let allowedExt = /(\mov|\avi|\mp4)$/i;
    errorOutput.innerHTML = '';
    if (!fileName) {
        errorOutput.style.color = 'red';
        errorOutput.style.backgroundColor = 'cornsilk';
        errorOutput.innerHTML = "No file selected!"
    }

    if (videoFile) {
        if (fileSize > 300000000) {
            errorOutput.style.color = 'red';
            errorOutput.style.backgroundColor = 'cornsilk';
            errorOutput.innerHTML = "File is too large! Max size 280MB!"
            return false;
        }
    } else if (!allowedExt.exec(fileName)) {
        errorOutput.style.color = 'red';
        errorOutput.style.backgroundColor = 'cornsilk';
        errorOutput.innerHTML = "Dateityp nicht unterstützt!"
        return false;
    }
    return;

}






