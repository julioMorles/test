$(document).ready(function () {

    cargarJugadores();
    juego();
    $("#ModalForm").validate({//creando las reglas para validar el formulario del modal
        rules: {
            nick: "required",
            nombre: "required",
            apellido: "required",
            email: {
                required: true,
                email: true
            },
        },
        messages: {
            nick: "Nick invalido",
            nombre: "Nombre invalido",
            apellido: "Apellidos invalidos",
            email: "EMail invalido",
            fondos:"Sus fondos deben ser superiores a 10000"
        }
    });

    $("#crearJugador").click(function () {// logica para la creacion del jugador
        $("#ModalCreate").modal('show');//mostrando modal
        $("#titleModal").html("Crear Jugador");// agregando el titulo
        localStorage.setItem("action","add"); //State para el guardar
    });

    $("#guardarModal").click(function () {
        let state = localStorage.getItem("action");// rescato el objeto state del localstorage
        let objEnviar = {
            "nombre":$("#nombre").val(),
            "apellidos":$("#apellido").val(),
            "nick":$("#nick").val(),
            "email":$("#email").val(),
        }; //creo el objeto con la informacion del jugador para ser enviado

        if($("#fondos").val() >= 10000){ objEnviar['fondos'] = $("#fondos").val() }
        switch (state) {
            case "add":
                if ($('#ModalForm').valid()){
                    $.ajax({
                        type: "post",
                        url: "api/public/api/jugadores",
                        data:objEnviar,
                        beforeSend:function(){
                            $("#loaddingModal").show();//mostrando modal//muestra el icono de carga
                        },
                        success : function(data)
                        {
                            cargarJugadores();//de ser afirmativa la respuesta del la api, el recarga la tabla
                            $("#loaddingModal").hide();//cerrando cargando
                            $("#ModalCreate").modal('hide');//cerrando modal
                        },
                        error : function (data) {

                        }
                    });
                }
            break;
            case "edit":
                let idJugador = localStorage.getItem("idJugador");
                if ($('#ModalForm').valid()){
                    $.ajax({
                        type: "PUT",
                        url: "api/public/api/jugadores/"+idJugador,
                        data:objEnviar,
                        beforeSend:function(){
                            $("#loaddingModal").show();//mostrando modal//muestra el icono de carga
                        },
                        success : function(data)
                        {
                            cargarJugadores();//de ser afirmativa la respuesta del la api, el recarga la tabla
                            $("#loaddingModal").hide();//cerrando cargando
                            $("#ModalCreate").modal('hide');//cerrando modal
                        },
                        error : function (data) {

                        }
                    });
                }
            break;
        }
    });

    $("#apostar").click(function () {
        juego();
    });
});

function cargarJugadores() {
    /*Se realiza la solicitud de los datos de los jugadores a la api
        * Esta retorna un objeto jSON con los datos
        * request ajax normal con metodo get
        */
    $.ajax({
        type: "get",
        url: "api/public/api/jugadores",
        beforeSend:function(){
            $("#loadding").show();//muestra el icono de carga
        },
        success : function(data)
        {
            if(data.data.length > 0){ // se verifica que existan jugadores
                let contenedor="<table class='table'>"; //Se crea el contenedor de los objetos html con la lista de los jugadores
                contenedor+="<thead>";
                contenedor+="<tr>";
                contenedor+="<th>Nick</th>";
                contenedor+="<th>Fondos</th>";
                contenedor+="<th>Apuesta</th>";
                contenedor+="<th>Accion</th>";
                contenedor+="</tr>";
                contenedor+="</thead>";
                contenedor+="<tbody>";
                data.data.forEach((jugador) => {
                    let apuesta;
                    if(jugador.apuesta != null ){
                        switch (jugador.apuesta) {
                            case 1 :
                                apuesta="<td style='color: white; background-color:red;'>Rojo</td>";
                                break;
                            case 2 :
                                apuesta="<td style='color: white; background-color:black;'>Negro</td>";
                                break;
                            case 3 :
                                apuesta="<td style='color: white; background-color:green;'>Verde</td>";
                                break;

                        }
                    } else {
                     apuesta ="<td style='color: black;'>Sin apostar</td>";
                    }
                    contenedor+="<tr>";
                    contenedor+="<td>"+ jugador.nick +"</td>";
                    contenedor+="<td>"+ jugador.fondos +"</td>";
                    contenedor+=apuesta;
                    contenedor+="<td><a onclick='editarJugador("+ jugador.id +")'><i class='far fa-edit'></i>&nbsp&nbsp</a><a onclick='eliminarJugador("+ jugador.id +")'><i class='fas fa-trash'></i></a></td>";
                    contenedor+="</tr>";
                });
                contenedor+="</tbody>";
                contenedor+="</table>";
                $("#conteJugadores").html(contenedor); // se serializa y envia los datos a la vista
            }
            $("#loadding").hide();//Esconde el icono de carga
        },
        error : function (data) {

        }
    });
}

function editarJugador(id){//logica para editar un jugador

    $.ajax({//hago get para traer la info del jugador
        type: "get",
        url: "api/public/api/jugadores/"+id,
        beforeSend:function(){
            $("#loadding").show();//muestra el icono de carga
        },
        success : function(data)
        {
           let jugador = data.data;

            $("#nombre").val(jugador.nombre);
            $("#apellido").val(jugador.apellidos);
            $("#nick").val(jugador.nick);
            $("#email").val(jugador.email);
            $("#fondos").val(jugador.fondos);
            $("#loadding").hide();//Esconde el icono de carga
        },
        error : function (data) {

        }
    });



    $("#ModalCreate").modal('show');//mostrando modal
    $("#titleModal").html("Editar Jugador");// agregando el titulo
    localStorage.setItem("action","edit"); //State para el guardar
    localStorage.setItem("idJugador",id); //State para el guardar
}

function eliminarJugador(id) {
    let confir = confirm("Esta seguro de eliminar el jugador?");

    if(confir){
        $.ajax({
            type: "delete",
            url: "api/public/api/jugadores/"+id,
            beforeSend:function(){
                $("#loadding").show();//muestra el icono de carga
            },
            success : function(data)
            {
                cargarJugadores();//de ser afirmativa la respuesta del la api, el recarga la tabla
                $("#loadding").hide();//cerrando cargando
            },
            error : function (data) {

            }
        });
    }
}

function juego() {

    $.ajax({
        type: "post",
        url: "api/public/api/apuesta",
        beforeSend:function(){
            $("#loadding").show();//muestra el icono de carga
        },
        success : function(data)
        {
            $("#resulId").val(data);
            switch (data) {
                case "1" :
                    $("#resultado").css("background-color", "red");
                    $("#resultado").html("<strong style='color: white'>Rojo</strong>");
                    break;
                case "2" :
                    $("#resultado").css("background-color", "black");
                    $("#resultado").html("<strong style='color: white'>Negro</strong>");
                    break;
                case "3" :
                    $("#resultado").css("background-color", "green");
                    $("#resultado").html("<strong style='color: white'>Verde</strong>");
                    break;
                default :
                    $("#resultado").html("<strong style='color: black'>No apuesta</strong>");
                    break;
            }
            cargarJugadores();//de ser afirmativa la respuesta del la api, el recarga la tabla
            $("#loadding").hide();
        },
        error : function (data) {
            $("#loadding").hide();
            alert("No se pudo apostar");
        }
    });

}
