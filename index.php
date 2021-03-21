<?php
/*
 Plugin name: delete_user_basic
 plugin uri: www.localhost.com
 Description: Short Code para eliminar los datos basicos del usuario
 Author: Luis Castillo
 Version: 0.1
 Author URI:
 */

add_shortcode("delete_user_basic","delete_user_basic");

function delete_user_basic(){
    global $wpdb;
    $errors = array();
    $mensaje_exito="";
    if(isset($_POST["codigo"]) && isset($_POST["code"]) && $_POST["codigo"] == $_POST["code"]){
        $_POST["submit_delete"] = "SI";
    }elseif(isset($_POST["codigo"]) && isset($_POST["code"])){
        $_POST["submit_delete"] = "SI";
        $errors[] = "Codigo de verificación erroneo.";
        $_POST["submit_email"] = "SI";
        $codigo = $_POST["codigo"];
    }
    if(isset($_POST["submit_delete"])){

        //Procedo a actualizar
        if(count($errors) == 0 ){

            $user_id = wp_delete_user( get_current_user_id() );
            //$user_id =5;
            if($user_id > 0){
                $mensaje_exito = "Se han eliminado su cuenta correctamente. Será redirigido dentro de 3 segundos";
            }
        }

    }elseif(isset($_POST["submit_email"])){
        $codigo = mt_rand();
        $id = get_current_user_id();
        $data_user = get_user_to_edit($id);
        $mensaje = "<p>Buenos días {$data_user->data->display_name} </p>
                    <p>Se envia este correo de verificación con un código para proceder a la eliminación de su cuenta.</p>
                    <br/> 
                    <p><b>$codigo</b></p>
                    <br/>                        
                    <p>Ingreselo en el formulario y siga el proceso</p>
                    <p>Saludos.</p>
                    ";
        //$data_user->data->user_email
        $return_mail = wp_mail( $data_user->data->user_email, "Código de verificación de eliminación de usuario", $mensaje );
        if($return_mail){
            //$mensaje_exito = "se envio el correo";
        }else{
            $errors[] = "Error al enviar el correo";
        }
    }

    ob_start();
    $path = 'assets/css/woocommerce-layout.css';
    wp_enqueue_style( "nombre", apply_filters( 'woocommerce_get_asset_url', plugins_url( $path, WC_PLUGIN_FILE ), $path ) );

    ?>
    <!--    <link rel="stylesheet" type="text/css" href="<?php echo content_url()."/plugins/woocommerce/assets/css/woocommerce-layout.css?ver=4.9.0"; ?>" media="screen" />
-->
    <div class="woocommerce-MyAccount-content">
        <?php if(count($errors) > 0 ){ ?>
            <div class="woocommerce-notices-wrapper"><ul class="woocommerce-error" role="alert">
                    <?php foreach ($errors as $error){?>
                        <li><?php echo $error ?></li>
                    <?php } ?>
                </ul>
            </div>
        <?php } ?>
        <?php if( $mensaje_exito != "" ){ ?>
            <div class="woocommerce-notices-wrapper"><ul class="woocommerce-message" role="alert">
                    <li><?php echo $mensaje_exito ?></li>
                </ul>
            </div>
        <script>
            setTimeout(function(){
                location.href = "<?php echo str_replace("wp-admin/","", get_admin_url() ) ?>";
            },3000);
        </script>
    </div>
        <?php }else{
        if($_POST["submit_email"]){?>
        <form class="woocommerce-EditAccountForm edit-account" action="<?php get_the_permalink();?>" method="post">
            <input type="hidden" name="submit_delete" id="submit_delete" value="SI" />
            <input type="hidden" name="codigo" id="codigo" value="<?php echo $codigo ?>" />
            <h1 align="center" style="text-transform: none">Por favor ingrese el código enviado a su correo electrónico</h1>
            <p class="woocommerce-form-row woocommerce-form-row--first form-row form-row-first">
                <label for="code">Codigo<span style="color:red;"></span></label>
                <br/>
                <input type="text" style="width: 100%" class="woocommerce-Input woocommerce-Input--text input-text" required name="code" id="code" autocomplete="given-name" value="<?php isset($_POST["code"])?$_POST["code"]:"" ?>">
            </p>
            <p align="center">
                <button type="submit" class="woocommerce-Button button" name="save_account_details" value="Guardar los cambios">Enviar</button>
                <button type="button" class="woocommerce-Button button" name="back" id="back" value="Atras">Atras</button>
            </p>

        </form>
        <script>
            var el = document.getElementById("back");
            el.addEventListener("click", atras);
            function atras(){
                location.href = "<?php echo str_replace("wp-admin/","", get_admin_url() ) ?>";
            }
        </script>
            <?php }else{?>
        <form class="woocommerce-EditAccountForm edit-account" action="<?php get_the_permalink();?>" method="post">
            <input type="hidden" name="submit_email" id="submit_email" value="SI" />
            <h1 align="center">¿Esta seguro de eliminar su cuenta? </h1>
            <p align="center">
                <button type="submit" class="woocommerce-Button button" name="save_account_details" value="Guardar los cambios">Eliminar cuenta</button>
                <button type="button" class="woocommerce-Button button" name="back" id="back" value="Atras">Atras</button>
            </p>

        </form>
        <script>
            var el = document.getElementById("back");
            el.addEventListener("click", atras);
            function atras(){
                location.href = "<?php echo str_replace("wp-admin/","", get_admin_url() ) ?>";
            }
        </script>
    </div>
    <?php
    }
    }
    return ob_get_clean();

}

