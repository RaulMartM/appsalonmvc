<?php

namespace Controllers;

use Model\Usuario as ModelUsuario;
use MVC\Router;
use Classes\email;


class LoginController{
    public static function login(Router $router) {

        $alertas = [];
        $auth= new ModelUsuario;

        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $auth = new ModelUsuario($_POST);
            $alertas = $auth->validarLogin();

            if(empty($alertas)){
                //Comprobar que el usuario existe

                $usuario = ModelUsuario::where('email', $auth->email);

                if($usuario){
                    //Verificar el password
                    $usuario -> comprobarPasswordAndVerificado($auth->password);
                    //autenticar usuario
                    session_start();

                    $_SESSION['id'] = $usuario->id;
                    $_SESSION['nombre'] = $usuario->nombre . " ". $usuario->apellido;
                    $_SESSION['email'] = $usuario->email;
                    $_SESSION['login'] = true;

                    //redireccionamiento

                    if($usuario->admin === "1"){
                        $_SESSION['admin'] = $usuario->admin ?? null;
                        header('Location: /admin');
                    }else{
                        header('Location: /cita');
                    }


                }else{
                    ModelUsuario::setAlerta('error', 'Usuario no encontrado');
                }

                //Verificar el password

                // debuguear($usuario);

            }
            // debuguear( $auth);
        }
        $alertas = ModelUsuario::getAlertas();
        $router->render('auth/login',[
            'alertas' => $alertas,
            'auth' => $auth
        ]);
    }
    public static function logout() {
        if(!isset($_SESSION)){
            session_start();
        }
        $_SESSION = [];

        

        header('Location:/');     

    }
    public static function olvide(Router $router) {
        $alertas = [];

        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $auth = new ModelUsuario($_POST);
            $alertas = $auth->validarEmail();

            if(empty($alertas)){
               $usuario = ModelUsuario::where('email', $auth->email);
               if($usuario && $usuario->confirmado){
                
                //Generar un token
                $usuario->crearToken();
                $usuario->guardar();

                //Enviar correo
                $email = new Email($usuario->nombre, $usuario->email, $usuario->token);
                $email->enviarInstrucciones();

                //Alerta de exito
                ModelUsuario::setAlerta('exito', 'Revisa tu email');

               }else{
                ModelUsuario::setAlerta('error', 'El usuario no existe o no está confirmado');
               }
            }
        }
        $alertas = ModelUsuario::getAlertas();
        $router->render('auth/olvide',[
            'alertas' => $alertas
        ]);
    }

    public static function recuperar(Router $router) {
        $alertas = [];
        $error = false;

        $token = s($_GET['token']);
        
        //Buascar usuario por el token

        $usuario = ModelUsuario::where('token', $token);

        if(empty($usuario)){
            ModelUsuario::setAlerta('error', 'Token no válido');
            $error = true;
        }

        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            //Leer el nuevo pass y guardarlo
            $password = new ModelUsuario($_POST);
            $alertas = $password->validarPassword();

            if(empty($alertas)){
                $usuario->password = null;
                $usuario->password = $password->password;
                $usuario->hashPassword();
                $usuario->token = null;

                $resultado = $usuario->guardar();

                if($resultado){
                    header('Location:/');
                }
            }
        }


        // debuguear($usuario);

        $alertas = ModelUsuario::getAlertas();
        $router->render('auth/recuperar-password',[
            'alertas' => $alertas,
            'error' => $error
        ]);
    }
    public static function crear(Router $router) {
        $usuario = new ModelUsuario;

        $alertas = []; //Alertas vacías

        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $usuario->sincronizar($_POST);
            $alertas = $usuario->validarNuevaCuenta();

            //revisar que alertas esté vacío
            if(empty($alertas)){
                //verificar que el usuario no esté registrado
                $resultado = $usuario->existeUsuario();
                if($resultado->num_rows){
                    $alertas = ModelUsuario::getAlertas();
                } else{
                    //hasear password
                    $usuario->hashPassword();

                    //Generar token unico
                    $usuario->crearToken();

                    //enviar el email
                    $email = new Email($usuario->nombre, $usuario->email, $usuario->token);
                    $email->enviarConfirmacion();

                    //crear el usuario
                    $resultado = $usuario->guardar();
                    // debuguear($resultado);
                    if($resultado){
                        header('Location: /mensaje');
                    }
                }
            }
        }
        $router->render('auth/crear-cuenta',[
            'usuario' => $usuario,
            'alertas' => $alertas
        ] );
    }

    public static function mensaje(Router $router){
        $router->render('auth/mensaje');
    }

    public static function confirmar(Router $router){
        $alertas = [];

        $token=s($_GET['token']);

        $usuario = ModelUsuario::where('token', $token);

        if(empty($usuario)){
            //Mostrar mensaje de error
            ModelUsuario::setAlerta('error', 'Token no válido');
        
        }else{
            //Modificar a usuario confirmado 
            $usuario->confirmado = "1";
            $usuario->token = null;
            $usuario->guardar();
            ModelUsuario::setAlerta('exito', 'Cuenta Verificada con éxito');

        }
        
        $alertas = ModelUsuario::getAlertas();

        $router->render('auth/confirmar-cuenta',[
            'alertas' => $alertas
        ]);
    }

}