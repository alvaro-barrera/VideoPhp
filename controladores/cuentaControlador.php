<?php
    if ($peticionAjax) {
        require_once "../core/mainModel.php";
    }else{
        require_once "./core/mainModel.php";
    }
    class cuentaControlador extends mainModel{
        public function datos_cuenta_controlador($codigo,$tipo){
            $codigo=mainModel::decryption($codigo);
            $tipo=mainModel::limpiar_cadena($tipo);

            if ($tipo=="admin") {
                $tipo="Administrador";
            } else {
                $tipo="Cliente";
            }
            

            return mainModel::datos_cuenta($codigo,$tipo);
        }

        public function actualizar_cuenta_controlador(){
            $CuentaCodigo=mainModel::decryption($_POST['CodigoCuenta-up']);
            $CuentaTipo=mainModel::decryption($_POST['tipoCuenta-up']);

            $query1=mainModel::ejecutar_consulta_simple("SELECT * FROM cuenta WHERE CuentaCodigo='$CuentaCodigo'");
            $DatosCuenta=$query1->fetch();

            $user=mainModel::limpiar_cadena($_POST['user-log']);
            $password=mainModel::limpiar_cadena($_POST['password-log']);
            $password=mainModel::encryption($password);

            if ($user!="" && $password!="") {
                if (isset($_POST['privilegio-up'])) {
                    $login=mainModel::ejecutar_consulta_simple("SELECT id FROM cuenta WHERE CuentaUsuario='$user' AND CuentaClave='$password'");
                } else {
                    $login=mainModel::ejecutar_consulta_simple("SELECT id FROM cuenta WHERE CuentaUsuario='$user' AND CuentaClave='$password' AND CuentaCodigo='$CuentaCodigo'");
                }

                if ($login->rowCount()==0) {
                    $alerta=[
                        "Alerta"=>"simple",
                        "Titulo"=>"Ocurrió un error inesperado",
                        "Texto"=>"El nombre de usuario y contraseña no coinciden con los datos de su cuenta",
                        "Tipo"=>"error"
                    ];
                    return mainModel::sweet_alert($alerta);
                    exit();
                }
                
            } else {
                $alerta=[
                    "Alerta"=>"simple",
                    "Titulo"=>"Ocurrió un error inesperado",
                    "Texto"=>"Para actualizar los datos de la cuenta debe ingresar el nombre de usuario y contraseña, intente nuevamente",
                    "Tipo"=>"error"
                ];
                return mainModel::sweet_alert($alerta);
                exit();
            }

            //Verificar usuario
            $CuentaUsuario=mainModel::limpiar_cadena($_POST['usuario-up']);
            if ($CuentaUsuario!=$DatosCuenta['CuentaUsuario']) {
                $query2=mainModel::ejecutar_consulta_simple("SELECT CuentaUsuario FROM cuenta WHERE CuentaUsuario='$CuentaUsuario'");
                if ($query2->rowCount()>=1) {
                    $alerta=[
                        "Alerta"=>"simple",
                        "Titulo"=>"Ocurrió un error inesperado",
                        "Texto"=>"El nombre de usuario que ingresó ya se encuentra registrado en el sistema",
                        "Tipo"=>"error"
                    ];
                    return mainModel::sweet_alert($alerta);
                    exit();
                }
            }

            //Verificar email
            $CuentaEmail=mainModel::limpiar_cadena($_POST['email-up']);
            if ($CuentaEmail!=$DatosCuenta['CuentaEmail']) {
                $query3=mainModel::ejecutar_consulta_simple("SELECT CuentaEmail FROM cuenta WHERE CuentaEmail='$CuentaEmail'");
                if ($query3->rowCount()>=1) {
                    $alerta=[
                        "Alerta"=>"simple",
                        "Titulo"=>"Ocurrió un error inesperado",
                        "Texto"=>"El email que ingresó ya se encuentra registrado en el sistema",
                        "Tipo"=>"error"
                    ];
                    return mainModel::sweet_alert($alerta);
                    exit();
                }
            }
            
            $CuentaGenero=mainModel::limpiar_cadena($_POST['optionsGenero-up']);
            if (isset($_POST['optionsEstado-up'])) {
                $CuentaEstado=mainModel::limpiar_cadena($_POST['optionsEstado-up']);

            } else {
                $CuentaEstado=$DatosCuenta['CuentaEstado'];
            }

            if ($CuentaTipo="admin") {
                if (isset($_POST['optionsPrivilegio-up'])) {
                    $CuentaPrivilegio=mainModel::decryption($_POST['optionsPrivilegio-up']);
                } else {
                    $CuentaPrivilegio=$DatosCuenta['CuentaPrivilegio'];
                }

                if ($CuentaGenero=="Masculino") {
                    $CuentaFoto="Male3Avatar.png";
                } else {
                    $CuentaFoto="Female3Avatar.png";
                }
                
                
            } else {
                $CuentaPrivilegio=$DatosCuenta['CuentaPrivilegio'];
                if ($CuentaGenero=="Masculino") {
                    $CuentaFoto="Male2Avatar.png";
                } else {
                    $CuentaFoto="Female2Avatar.png";
                }
            }
            
            //Verificar cambio de clave
            $passwordN1=mainModel::limpiar_cadena($_POST['newPassword1-up']);
            $passwordN2=mainModel::limpiar_cadena($_POST['newPassword2-up']);
            if ($passwordN1!="" || $passwordN2!="") {
                if ($passwordN1==$passwordN2) {
                    $CuentaClave=mainModel::encryption($passwordN1);
                } else {
                    $alerta=[
                        "Alerta"=>"simple",
                        "Titulo"=>"Ocurrió un error inesperado",
                        "Texto"=>"Las contraseñas no coinciden, por favor intente nuevamente",
                        "Tipo"=>"error"
                    ];
                    return mainModel::sweet_alert($alerta);
                    exit();
                }
                
            } else {
                $CuentaClave=$DatosCuenta['CuentaClave'];
            }
            //Enviando datos al modelo
            $datosUpdate=[
                "CuentaPrivilegio"=>$CuentaPrivilegio,
                "CuentaCodigo"=>$CuentaCodigo,
                "CuentaUsuario"=>$CuentaUsuario,
                "CuentaClave"=>$CuentaClave,
                "CuentaEmail"=>$CuentaEmail,
                "CuentaEstado"=>$CuentaEstado,
                "CuentaPrivilegio"=>$CuentaPrivilegio,
                "CuentaPrivilegio"=>$CuentaPrivilegio,
                "CuentaGenero"=>$CuentaGenero,
                "CuentaFoto"=>$CuentaFoto
            ];
            if (mainModel::actualizar_cuenta($datosUpdate)) {
                if (!isset($_POST['privilegio-up'])) {
                    session_start(['name'=>'SBP']);
                    $_SESSION['usuario_sbp']=$CuentaUsuario;
                    $_SESSION['foto_sbp']=$CuentaFoto;
                }
                $alerta=[
                    "Alerta"=>"recargar",
                    "Titulo"=>"Cuenta actualizada",
                    "Texto"=>"Los datos de la cuenta se actualizaron de manera correcta",
                    "Tipo"=>"success"
                ];
            } else {
                $alerta=[
                    "Alerta"=>"simple",
                    "Titulo"=>"Ocurrió un error inesperado",
                    "Texto"=>"Lo sentimos, no hemos podido actualizar los datos, intente nuevamente",
                    "Tipo"=>"error"
                ];
            }
            return mainModel::sweet_alert($alerta);

        }

        }
    