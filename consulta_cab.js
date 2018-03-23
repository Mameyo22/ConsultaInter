$(document).ready(function(){
    //Home Button
    $("#home").click(function(){
        //redirigir a home
        $(location).attr("href","home.php");
    });
    //Menu Button
    $("#menu").click(function(){
        $(location).attr("href","menu.php");
    });
    //Search Button
    $("#buscar").click(function(){
        var cuenta = $("#cuenta").val();
        var nombre = $("#nombre").val();
        var documento = $("#documento").val();
        var dataString = 'accion=1&cuenta='+ cuenta + '&nombre='+ nombre + '&documento='+documento;
        $.ajax({
            url: "consulta_func.php",
            type: "POST",
            cache: false,
            data: dataString,
            success:function(result){
                $("#resultado").html(result);
            }
        });     
    });
 });


 