function poll() {
    setInterval(function () {
        location.reload();
    }, 30000);
}

document.addEventListener("DOMContentLoaded", poll, false);