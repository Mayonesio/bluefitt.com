<?php
 
  if($_SERVER["REQUEST_METHOD"] === "POST")
    {
 
    // Colocamos la clave secreta de reCAPTCHA v3 
    define("SECRET_KEY", '6LcOYt0aAAAAAG0d5WCbyqA9xmoJRdCPR3ZI5hWf'); 
 
    $token = $_POST['token'];
    $action = $_POST['action'];
     
    // Mediante CURL hago un Post a la api de reCaptcha 
    $datos = curl_init();
    curl_setopt($datos, CURLOPT_URL,"https://www.google.com/recaptcha/api/siteverify");
    curl_setopt($datos, CURLOPT_POST, 1);
    
    // En el Post a la api de reCaptcha envio la Secret Key y el Token generado en la vista HTML
    curl_setopt($datos, CURLOPT_POSTFIELDS, http_build_query(
      array(
        'secret' => SECRET_KEY, 
        'response' => $token
      )
    ));
 
    // Obtengo una respuesta de reCaptcha y los datos obtenidos los decodifico para poder verificarlos 
    curl_setopt($datos, CURLOPT_RETURNTRANSFER, true); 
    $respuesta = curl_exec($datos);    
    curl_close($datos);
    $datos_respuesta = json_decode($respuesta, true);
    
     
    // Verificamos los datos 
    if($datos_respuesta["success"] == '1' && $datos_respuesta["action"] == $action && $datos_respuesta["score"] >= 0.4) {
 
      // Si no es un robot hago una redirección con un mensaje 
      $puntaje = "<p><span style=color:green;font-weight:bold;>Puntaje: </span>".json_encode($datos_respuesta["score"])."</p>";
      $mensaje = "<p><span style=color:green;font-weight:bold;>Resultado: </span>No eres un robot. </p>";
      header("Location: recibido.html");
      if(isset($_POST['email'])){
	  
        $name =$_POST["name"];
        $from =$_POST["email"];
        $comment=$_POST["comment"];
        
        // Email Receiver Address
        $receiver="julio.ramirez@bluefitt.com";
        $subject="CONTACTO DESDE FORMULARIO BLUEFITT.";
        

    
        $message = "
        <html>
        <head>
        <title>HTML email</title>
        </head>
        <body>
        <table width='50%' border='0' align='center' cellpadding='0' cellspacing='0'>
        <tr>
        <td colspan='2' align='center' valign='top'><img style=' margin-top: 15px; ' src='http://www.galarza.pro/images/logo-email.png' ></td>
        </tr>
        <tr>
        <td width='50%' align='right'>&nbsp;</td>
        <td align='left'>&nbsp;</td>
        </tr>
        <tr>
        <td align='right' valign='top' style='border-top:1px solid #dfdfdf; font-family:Arial, Helvetica, sans-serif; font-size:13px; color:#000; padding:7px 5px 7px 0;'>Name:</td>
        <td align='left' valign='top' style='border-top:1px solid #dfdfdf; font-family:Arial, Helvetica, sans-serif; font-size:13px; color:#000; padding:7px 0 7px 5px;'>".$name."</td>
        </tr>
        <tr>
        <td align='right' valign='top' style='border-top:1px solid #dfdfdf; font-family:Arial, Helvetica, sans-serif; font-size:13px; color:#000; padding:7px 5px 7px 0;'>Email:</td>
        <td align='left' valign='top' style='border-top:1px solid #dfdfdf; font-family:Arial, Helvetica, sans-serif; font-size:13px; color:#000; padding:7px 0 7px 5px;'>".$from."</td>
        </tr>
        <tr>
        <td align='right' valign='top' style='border-top:1px solid #dfdfdf; border-bottom:1px solid #dfdfdf; font-family:Arial, Helvetica, sans-serif; font-size:13px; color:#000; padding:7px 5px 7px 0;'>Message:</td>
        <td align='left' valign='top' style='border-top:1px solid #dfdfdf; border-bottom:1px solid #dfdfdf; font-family:Arial, Helvetica, sans-serif; font-size:13px; color:#000; padding:7px 0 7px 5px;'>".nl2br($comment)."</td>
        </tr>
        </table>
        </body>
        </html>
        ";
    // Always set content-type when sending HTML email
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    // More headers
    $headers .= 'From: <'.$from.'>' . "\r\n";
       if(mail($receiver,$subject,$message,$headers))  
       {
           //Success Message
    
          echo '<SCRIPT>window.open("./recibido.html","_blank");</SCRIPT>';
       }
       else
       {	
            //Fail Message
          echo '<SCRIPT>window.open("./norecibido.html");</SCRIPT>';
       }
    
    }
 
    } else {
 
      // Si es un robot hago una redirección con un mensaje 
      $puntaje = "<p> <span style=color:red;font-weight:bold;>Puntaje: </span>".json_encode($datos_respuesta["score"])."</p>";
      $mensaje = "<p> <span style=color:red;font-weight:bold;>Resultado: </span>Tú eres un robot. </p>";
      header("Location: norecibido.html");
 
    }
 
  }

  ?>
  