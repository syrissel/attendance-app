function startTime() {
    var today = new Date();
    var h = today.getHours();
    var m = today.getMinutes();
    var s = today.getSeconds();
    var time = "";
    m = checkTime(m);
    s = checkTime(s);
    time =  formatDayTime(h, m, s);
    document.getElementById('time').innerHTML = "<h3>" + time + "</h3>";
    var snackbar = document.getElementById("snack_time");
    if (snackbar) {
      snackbar.innerHTML = "<h1>" + time + "</h1>";
    }
  
    // Use recursion to loop the function.
    var t = setTimeout(startTime, 100);
}
  
// add zero in front of numbers < 10
function checkTime(i) {
    return (i < 10) ? "0" + i : i;
}

function checkHours(h) {
if (h == 0) {
    h = "12";
} else if (h > 13) {
    h = h - 12;
}
return h;
}

function formatDayTime(h, m, s) {
return (h < 12) ? (checkHours(h) + ":" + m + ":" + s + " AM") : (checkHours(h) + ":" + m + ":" + s + " PM");
}

startTime();