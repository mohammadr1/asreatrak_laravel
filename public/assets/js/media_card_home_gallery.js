document.addEventListener("DOMContentLoaded", function () {

    const modal = document.getElementById("mediaModal");
    const modalImage = document.getElementById("modalImage");
    const modalVideo = document.getElementById("modalVideo");
    const modalVideoSource = document.getElementById("modalVideoSource");
    const closeBtn = document.querySelector(".close-modal");

    window.openMedia = function (src, type) {

        modal.style.display = "flex";
        modal.classList.remove('d-none');

        if (type === "image") {

            modalImage.style.display = "block";
            modalVideo.style.display = "none";

            modalImage.src = src;
        }

        if (type === "video") {

            modalImage.style.display = "none";
            modalVideo.style.display = "block";

            modalVideoSource.src = src;

            modalVideo.load();
        }
    };

    closeBtn.onclick = function () {

        modal.style.display = "none";

        modal.classList.add('d-none');

        modalVideo.pause();
    };

    modal.onclick = function (e) {

        if (e.target === modal) {

            modal.style.display = "none";

            modalVideo.pause();
        }
    };

});