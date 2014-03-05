var amplia_footer = function() {
    // Ampliamos el alto del footer hasta el fondo de la página en el caso
    // de que el contenido sea demasiado pequeño y el footer se quede por 
    // encima del mismo
    var diferencia = $(window).height() - ($('footer').height() + $('footer').offset().top);
    if (diferencia > 0) {
        var altura_footer = $('footer').height();
        $('footer').height(altura_footer + diferencia - 50);
    } else {
        $('footer').height('auto');
    }
    return true;
};
