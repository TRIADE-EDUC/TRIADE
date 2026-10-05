function openVideo(url) {
        const modal = document.getElementById("videoModal");
        const frame = document.getElementById("youtubeFrame");
        frame.src = url+'?autoplay=1';
        modal.style.display = "flex";
}

function closeVideo() {
      const modal = document.getElementById("videoModal");
      const frame = document.getElementById("youtubeFrame");
      modal.style.display = "none";
      frame.src = ""; // stoppe la lecture
}

// Ferme la fenêtre si on clique en dehors
window.onclick = function (event) {
        const modal = document.getElementById("videoModal");
        if (event.target === modal) {
                closeVideo();
        }
};
