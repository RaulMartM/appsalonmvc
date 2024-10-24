<?php 

namespace Model;


class Usuario extends ActiveRecord{
    //base de datos
    protected static $tabla = 'usuarios';
    protected static $columnasDB = ['id', 'nombre', 'apellido','email', 'telefono', 'password', 'admin', 'confirmado', 'token'];
    
    public $id;
    public $nombre;
    public $apellido;
    public $email;
    public $telefono;
    public $password;
    public $admin;
    public $confirmado;
    public $token;

    public function __construct($args=[]){
        $this->id = $args['id'] ?? null;
        $this->nombre = $args['nombre'] ?? '';
        $this->apellido = $args['apellido'] ?? '';
        $this->email = $args['email'] ?? '';
        $this->telefono = $args['telefono'] ?? '';
        $this->password = $args['password'] ?? '';
        $this->admin = $args['admin'] ?? '0';
        $this->confirmado = $args['confirmado'] ?? '0';
        $this->token = $args['token'] ?? '';
    }

    //Mensajes de validación para la creación de la cuenta

    public function validarNuevaCuenta(){

        
        if (!$this->nombre){
            self::$alertas['error'][] = 'El nombre es obligatorio';
        } elseif(!$this->apellido){
            self::$alertas['error'][] = 'El apellido es obligatorio';
        }elseif (!$this->telefono){
            self::$alertas['error'][] = 'El telefono es obligatorio';
        }elseif (strlen($this->telefono) <> 10){
            self::$alertas['error'][] = 'Ingresa un número telefónico válido';
        }elseif (!$this->email){
            self::$alertas['error'][] = 'El email es obligatorio';
        }elseif (!$this->password){
            self::$alertas['error'][] = 'El password es obligatorio';
        } elseif (strlen($this->password)< 8){
            self::$alertas['error'][] = 'El password requiere mínimo 8 caracteres';
        }
        return self::$alertas;
    }

    public function validarEmail(){
        if (!$this->email){
            self::$alertas['error'][] = 'El email es obligatorio';
        }
        return self::$alertas;
    }

    public function validarLogin(){
        if(!$this->email){
            self::$alertas['error'][] = 'El email es obligatorio';
        }elseif(!$this->password){
            self::$alertas['error'][] = 'Tu contraseña es obligatoria';
        }

        return self::$alertas;
    }

    public function validarPassword(){
        if (!$this->password){
            self::$alertas['error'][] = 'El password es obligatorio';
        } elseif (strlen($this->password)< 8){
            self::$alertas['error'][] = 'El password requiere mínimo 8 caracteres';
        }

        return self::$alertas;
    }

    //REvisa si el usuario existe
    public function existeUsuario(){
        $query = " SELECT * FROM  " . self::$tabla. " WHERE email =  '" . $this->email . "' LIMIT 1";  

        $resultado = self::$db->query($query);

        if($resultado->num_rows){
            self::$alertas['error'][]= 'El usuario ya está registrado';
        }
        return $resultado;
    }

    public function hashPassword(){
        $this->password = password_hash($this->password, PASSWORD_BCRYPT);
    }

    public function crearToken(){
        $this->token = uniqid();
    }

    public function comprobarPasswordAndVerificado($password) {
        $resultado = password_verify($password, $this->password);
        
        if(!$resultado || !$this->confirmado) {
            self::$alertas['error'][] = 'Password Incorrecto o tu cuenta no ha sido confirmada';
        } else {
            return true;
        }
    }
}
