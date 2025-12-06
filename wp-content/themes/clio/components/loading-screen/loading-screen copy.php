<?php
/**
 * Created by PhpStorm.
 * User: sergio
 * Date: 05/02/16
 * Time: 19:09
 */ ?>
<!DOCTYPE html><html lang="es" class="no-js" style="background:white;min-height:1200px">
<head><meta charset="UTF-8"><title>(Acimut Sphere)</title>
<meta name="viewport" content="width=device-width, initial-scale=1"> 
<style type="text/css">
.sphere {
    width: 500px; height: 500px;
    background:  #88e2d6;
    border-radius: 50%;
    position: absolute;
    top: 50%; left: 50%;
    margin: -250px;
    transform: scale(0) rotate(-15deg);
    overflow: hidden;
    transition: 1s ease-out;
    }

.sphere.show {
    transform: scale(.5) rotate(-18.8deg);
}
.equator {
    width: 500px; height: 500px;
    border: 20px solid white;
    border-top: 10px solid transparent;
        border-bottom: 30px solid white;
    margin: -20px;
    transform: rotateX(75deg);
    _background: #00000022;
    border-radius: 50%;
    position: relative;
    animation: rotateX 3s linear;
    }
    @keyframes rotateX {
        to {transform: rotateX(75deg) rotate(360deg) ;}
    }
.half {
    position: absolute;
    width: 500px; height: 250px;
    top: 0; left: 0;
    transform-origin: bottom;
    background: #88e2d6;
    animation: rotate360 2s linear;
    }
    @keyframes rotate360 {
        from {transform: rotate(0deg) ;}
        to {transform:  rotate(360deg) ;}
    }
.radius {
    position: absolute;
    width: 0; height: 0;
    top: 50%; left: 50%;
    transform-origin: top left;
    box-sizing: border-box;
    height: 250px;
    }
.radiate .radius.-right {
    border-top: 20px solid white;
    animation: radius1 1s linear forwards;
    width: 250px; 
    }
    @keyframes radius1 {
        0%  {width: 0; }
        50%{width: 250px;}
        100%{width: 250px;}
    }
.radiate .radius.-front {
    border-top: 10px solid white;
    animation: radius2 2s linear;
    transform: rotate(90deg);
    width: 250px; 
    }
    @keyframes radius2 {
        0%  {width: 0; border-top: 20px solid white;transform: rotate(0deg)}
        25% {width: 250px;  border-top: 20px solid white; transform: rotate(0deg)}
        100% {width: 250px; border-top: 10px solid white; transform: rotate(90deg)}
    }
}
</style>
<script type='text/javascript' src='http://local-cdn.lh/js/jquery.2.2.4.min.js'></script>
<script>document.documentElement.className = 'js';
var run = function(){
    $O = $('.sphere');
    setTimeout(function() {
        $O.addClass('show');
    },500);
    setTimeout(function() {
        $O.addClass('radiate');
    },2750);
};
$(document).ready(run);

</script>
</head><body>

 <div class="sphere">
    <div class="plain">
    
    </div>
    <div class="equator">
        <div class="half -back"></div>
        <div class="radius -right"></div>
        <div class="radius -front"></div>
    </div>
 </div>
 </body></html> 