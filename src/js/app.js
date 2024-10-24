let paso = 1;
const pasoInicial = 1;
const pasoFinal = 3;

const cita = {
    nombre: '',
    id: '',
    fecha: '',
    hora: '',
    servicios: []
}

document.addEventListener('DOMContentLoaded', function(){
    iniciarApp();
});

function iniciarApp(){
    mostrarSeccion(); //muestra y oculta las secciones
    tabs(); //cambia la seccion cuando se presionan los tabs
    botonesPaginador(); //Agrega o quita los botones del paginador
    paginaSiguiente();
    paginaAnterior();

    consultarApi(); //Consulta la api en el backend de php

    nombreCliente(); //añade el nombre del cliente al objeto de cita
    seleccionarFercha(); //añade la fecha a la cita
    seleccionarHora(); //añade la hora de la cita en el objeto
    idCliente();
    mostrarResumen();
}

function mostrarSeccion(){
    //ocultar seccion que tenga la clase mostrar
    const seccionAnterior = document.querySelector('.mostrar');
    if (seccionAnterior){
        seccionAnterior.classList.remove('mostrar');
    }
    
    //seleccionar la seccion con el paso
    const pasoSelector = `#paso-${paso}`;
    const seccion= document.querySelector(pasoSelector);
    seccion.classList.add('mostrar');

    //quita la clase actual al tab anterior

    const tabAnterior = document.querySelector('.actual');
    // console.log(tabAnterior);
    if(tabAnterior){
       tabAnterior.classList.remove('actual'); 
    }

    //Resalta el tab actual
    const tab = document.querySelector(`[data-paso="${paso}"]`);
    tab.classList.add('actual');

}

function tabs(){
    const botones = document.querySelectorAll('.tabs button');

    botones.forEach(boton=>{
        boton.addEventListener('click', function(e){
            paso=parseInt(e.target.dataset.paso);
            mostrarSeccion();
            botonesPaginador();
            
        });
    });
}

function botonesPaginador(){
    const paginaAnterior = document.querySelector('#anterior');
    const paginaSiguiente = document.querySelector('#siguiente');

    if (paso === 1){
        paginaAnterior.classList.add('ocultar');
        paginaSiguiente.classList.remove('ocultar');
    }else if(paso === 3) {
        paginaAnterior.classList.remove('ocultar');
        paginaSiguiente.classList.add('ocultar');
        mostrarResumen();
    }else{
        paginaAnterior.classList.remove('ocultar');
        paginaSiguiente.classList.remove('ocultar');
    }
    mostrarSeccion();
}

function paginaAnterior(){
    const paginaAnterior = document.querySelector('#anterior');
    paginaAnterior.addEventListener('click', function(){
        if (paso<= pasoInicial) return;
        paso--;
        botonesPaginador();
    });
}

function paginaSiguiente(){
    const paginaSiguiente = document.querySelector('#siguiente');
    paginaSiguiente.addEventListener('click', function(){
        if (paso>= pasoFinal) return;
        paso++;
        botonesPaginador();
    });
}

async function consultarApi(){
    try {
        const url = '/api/servicios';
        const resultado = await fetch(url);
        const servicios = await resultado.json();
        moastrarServicios(servicios);

    } catch (error) {
        console.log(error);
    }
}

function moastrarServicios(servicios){
    servicios.forEach(servicio => {
        const {id, nombre, precio} = servicio;

        const nombreServicio = document.createElement('P');
        nombreServicio.classList.add('nombre-servicio');
        nombreServicio.textContent= nombre;

        const precioServicio = document.createElement('P');
        precioServicio.classList.add('precio-servicio');
        precioServicio.textContent= `$${precio}`;

        const servicioDiv = document.createElement('DIV');
        servicioDiv.classList.add('servicio');
        servicioDiv.dataset.idServicio= id;
        servicioDiv.onclick = function(){
            seleccionarServicio(servicio);
        };

        servicioDiv.appendChild(nombreServicio);
        servicioDiv.appendChild(precioServicio);

        document.querySelector('#servicios').appendChild(servicioDiv);

    });
}

function seleccionarServicio(servicio){
    const{servicios} = cita;
    const{id} = servicio;
    const divServicio = document.querySelector(`[data-id-servicio="${id}"]`);
    //comprobar si un servicio ya fue agregado
    if( servicios.some(agregado => agregado.id === id) ){  //.some sorve para verificar que exista dentro del arreglo
        //eliminarlo
        cita.servicios = servicios.filter( agregado => agregado.id !== id );
        divServicio.classList.remove('seleccionado');
    }else{
        //agregarlo
        cita.servicios = [...servicios, servicio];
        divServicio.classList.add('seleccionado');
    }
}
function idCliente() {
    cita.id = document.querySelector('#id').value;
}

function nombreCliente(){
    cita.nombre = document.querySelector('#nombre').value;
}

function seleccionarFercha(){
    const inputFecha = document.querySelector('#fecha');
    inputFecha.addEventListener('input', function(e){
        const dia=new Date(e.target.value).getUTCDay();
        if([1,2].includes(dia)){
            e.target.value='';
            mostrarAlerta('Recuerda que lunes y martes no tenemos servicio', 'error', '.formulario');
        }else{
            cita.fecha = e.target.value;
        }

    });
}

function seleccionarHora(){
    const inputHora = document.querySelector('#hora');
    inputHora.addEventListener('input', function(e){
        const horaCita = e.target.value;
        const hora = horaCita.split(':')[0];

        if(hora < 10 || hora > 18){
            e.target.value = '';
            mostrarAlerta('Hora no válida', 'error', '.formulario');  
        }else{
            cita.hora = e.target.value;
        }
    });
}

function mostrarResumen(){
    const resumen =document.querySelector('.contenido-resumen');

    //Limíar el contenido de resumen

    while(resumen.firstChild){
        resumen.removeChild(resumen.firstChild);
    }

    if (Object.values(cita).includes('') || cita.servicios.length === 0){
        mostrarAlerta('faltan datos de cervicios, fecha u hora', 'error', '.contenido-resumen', false);
        return;
    }

    // Formatear el div de resumen

    const {nombre, fecha, hora, servicios} = cita;

   

    //heading para servicios en resumen

    const headingServicios = document.createElement('H3');
    headingServicios.textContent = 'Resumen de Servicios';
    resumen.appendChild(headingServicios);


    //iterando y mostrando los servicios

    servicios.forEach(servicio =>{
        const {id, precio, nombre} = servicio;
        const contenidoServicio = document.createElement('DIV');
        contenidoServicio.classList.add('contenedor-servicio');

        const textoServicio = document.createElement('P');
        textoServicio.textContent = nombre;

        const precioServicio = document.createElement('P');
        precioServicio.innerHTML = `<span>Precio:</span> $${precio}`;

        contenidoServicio.appendChild(textoServicio);
        contenidoServicio.appendChild(precioServicio);

        resumen.appendChild(contenidoServicio);

    });

     //heading para cita

     const headingCita = document.createElement('H3');
     headingCita.textContent = 'Resumen de Cita';
     resumen.appendChild(headingServicios);
    
    const nombreCliente = document.createElement('P');
    nombreCliente.innerHTML = `<span>Nombre:</span> ${nombre}`;

    //Formatear fecha

    const fechaObj = new Date(fecha);

    const mes = fechaObj.getMonth();
    const dia = fechaObj.getDate()+2;
    const year = fechaObj.getFullYear();

    const fechaUTC = new Date(Date.UTC(year, mes, dia));
    const opciones = {weekday: 'long', year: 'numeric', month: 'long', day:'numeric'};
    const fechaFormateada = fechaUTC.toLocaleDateString('es-MX', opciones);
    console.log(fechaFormateada);

    const fechaCita = document.createElement('P');
    fechaCita.innerHTML = `<span>Fecha:</span> ${fechaFormateada}`;

    const horaCita = document.createElement('P');
    horaCita.innerHTML = `<span>Hora:</span> ${hora} horas`;

    //Botón para crear una cita

    const botonReservar = document.createElement('BUTTON');
    botonReservar.classList.add('boton');
    botonReservar.textContent = 'Reservar cita';
    botonReservar.onclick = reservarCita;


    resumen.appendChild(nombreCliente);
    resumen.appendChild(fechaCita);
    resumen.appendChild(horaCita);
    resumen.appendChild(botonReservar);

}

function mostrarAlerta(mensaje, tipo , elemento, desaparece = true){
    const alertaPrevia = document.querySelector('.alerta');

    if(alertaPrevia) {
        alertaPrevia.remove();
    };  

    const alerta = document.createElement('DIV');
    alerta.textContent = mensaje;
    alerta.classList.add('alerta');
    alerta.classList.add(tipo);
    const referencia = document.querySelector(elemento);
    referencia.appendChild(alerta);

    if(desaparece){
        //eliminacion de la alerta
        setTimeout(() => {
            alerta.remove();
        }, 3000);
    }
}

async function reservarCita(){
    const{ nombre, id, fecha, hora, servicios} = cita;
    const idServicios = servicios.map(servicio => servicio.id);

    const datos = new FormData();

    // datos.append('nombre', nombre);
    datos.append('fecha', fecha);
    datos.append('hora', hora);
    datos.append('usuarioId', id);
    datos.append('servicios', idServicios);


    try {
            //peticion hacia la api

        const url = '/api/citas';
        

        const respuesta = await fetch(url, {
            method: 'POST',
            body: datos
        } );

        const resultado = await respuesta.json();

        console.log(resultado);

        if (resultado.resultado){
            Swal.fire({
                icon: "success",
                title: "Cita Creada",
                text: "Tu cita ha sido agendada",
                button: 'OK'
                // footer: '<a href="#">Why do I have this issue?</a>'
            }).then( () => {
                setTimeout(()=> {
                    window.location.reload();
                }, 1000);
            });
        }
    } catch (error) {
        Swal.fire({
            icon: "error",
            title: "Error",
            text: "Hubo un error al guardar la cita",
        });
    }

    




    //console.log([...datos]);
}
