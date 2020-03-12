<?php
    $peticionAjax=true;
    require_once "../core/configGeneral.php";
    if (isset($_POST['CodigoCuenta-up'])  ) {
       
        require_once "../controladores/cuentaControlador.php";
        $cuenta=new cuentaControlador();

        if (isset($_POST['CodigoCuenta-up']) && isset($_POST['tipoCuenta-up']) && isset($_POST['usuario-up'])) {
            echo $cuenta->actualizar_cuenta_controlador();
        }

    } else {
        session_start(['name' => 'SBP']);
        session_destroy();
        echo '<script> window.location.href="'.SERVERURL.'login/" </script>';
    }
    