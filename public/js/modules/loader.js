const loaderContainer = document.querySelector('.loader-container');

export const displayLoading = () => {
  if (loaderContainer) loaderContainer.classList.remove('active');

};

export const hideLoading = () => {
  if (loaderContainer) loaderContainer.classList.add('active');
};

