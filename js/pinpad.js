// function load() {
//     alert("alert");

//     let btnGo = document.getElementById("go");
//     btnGo.addEventListener("click", showThing, false);

    
function load() {
    var pin = document.getElementById('code');

    $('#pin1').click(function() {
        $('#code').val($('#code').val() + '1');
        animate(this);
    })

    $('#pin2').click(function() {
        $('#code').val($('#code').val() + '2');
        animate(this);
    })
    
    $('#pin3').click(function() {
        $('#code').val($('#code').val() + '3');
        animate(this);
    })
    
    $('#pin4').click(function() {
        $('#code').val($('#code').val() + '4');
        animate(this);
    })

    $('#pin5').click(function() {
        $('#code').val($('#code').val() + '5');
        animate(this);
    })

    $('#pin6').click(function() {
        $('#code').val($('#code').val() + '6');
        animate(this);
    })

    $('#pin7').click(function() {
        $('#code').val($('#code').val() + '7');
        animate(this);
    })

    $('#pin8').click(function() {
        $('#code').val($('#code').val() + '8');
        animate(this);
    })

    $('#pin9').click(function() {
        $('#code').val($('#code').val() + '9');
        animate(this);
    })

    $('#pin0').click(function() {
        $('#code').val($('#code').val() + '0');
        animate(this);
    })

    $('#del').click(function() {
        $('#code').val($('#code').val().slice(0, -1));
        animate(this);
    })

    document.getElementById("pin1").addEventListener('touchstart', function(e) {
        var touchobj = e.changedTouches[0];
        pin.value = pin.value + '1';
        animate(this);
        e.preventDefault();
    }, false);
    document.getElementById("pin2").addEventListener('touchstart', function(e) {
        var touchobj = e.changedTouches[0];
        pin.value = pin.value + '2';
        animate(this);
        e.preventDefault();
    }, false);
    document.getElementById("pin3").addEventListener('touchstart', function(e) {
        var touchobj = e.changedTouches[0];
        pin.value = pin.value + '3';
        animate(this);
        e.preventDefault();
    }, false);
    document.getElementById("pin4").addEventListener('touchstart', function(e) {
        var touchobj = e.changedTouches[0];
        pin.value = pin.value + '4';
        animate(this);
        e.preventDefault();
    }, false);
    document.getElementById("pin5").addEventListener('touchstart', function(e) {
        var touchobj = e.changedTouches[0];
        pin.value = pin.value + '5';
        animate(this);
        e.preventDefault();
    }, false);
    document.getElementById("pin6").addEventListener('touchstart', function(e) {
        var touchobj = e.changedTouches[0];
        pin.value = pin.value + '6';
        animate(this);
        e.preventDefault();
    }, false);
    document.getElementById("pin7").addEventListener('touchstart', function(e) {
        var touchobj = e.changedTouches[0];
        pin.value = pin.value + '7';
        animate(this);
        e.preventDefault();
    }, false);
    document.getElementById("pin8").addEventListener('touchstart', function(e) {
        var touchobj = e.changedTouches[0];
        pin.value = pin.value + '8';
        animate(this);
        e.preventDefault();
    }, false);
    document.getElementById("pin9").addEventListener('touchstart', function(e) {
        var touchobj = e.changedTouches[0];
        pin.value = pin.value + '9';
        animate(this);
        e.preventDefault();
    }, false);
    document.getElementById("pin0").addEventListener('touchstart', function(e) {
        var touchobj = e.changedTouches[0];
        pin.value = pin.value + '0';
        animate(this);
        e.preventDefault();
    }, false);
    document.getElementById("del").addEventListener('touchstart', function(e) {
        var touchobj = e.changedTouches[0];
        pin.value = pin.value.slice(0, -1);
        animate(this);
        e.preventDefault();
    }, false);

    function animate(element) {
        element.firstElementChild.style.visibility = "hidden";
        setTimeout(function() {
            element.firstElementChild.style.visibility = "visible";
        }, 180);
        //$(element).animate({"background":"#58FF3C"});
        // element.style.background = "#58FF3C";
        // setTimeout(function() {
        //     element.style.background = "#FFF";
        // }, 100);
    }

}

// }

// function showThing() {

//     let pin = document.getElementById('code');
//     alert(pin.value);
// }



document.addEventListener("DOMContentLoaded", load, false);