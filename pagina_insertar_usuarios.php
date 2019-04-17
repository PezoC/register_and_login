<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Documento sin título</title>
<style media="screen">
	.correo_usado{
		align-content: center;
		color: green;
		font-size: 30px;
	}

</style>
</head>

<body>

<?php

	$usuario= $_POST["usu"];
	$contrasenia= $_POST["contra"];
	$correo = $_POST["email"];

	$pass_cifrado = password_hash($contrasenia, PASSWORD_DEFAULT, array("cost"=>15));
	/* array("cost"=>12): indica que el coste del algoritmo (es decir, la fuerza) será de 12 (por defecto es de 10) */



	try{

		$base=new PDO('mysql:host=localhost; dbname=pruebas', 'root', '');

		$base->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		$base->exec("SET CHARACTER SET utf8");


		$sql_verification = $base->prepare("SELECT * FROM usuarios WHERE correo = :correo_check");

		$sql_verification->bindParam(':correo_check', $correo);

		$sql_verification->execute();

		$resultado_verification = $sql_verification->fetchAll();


		if ($resultado_verification) {

			echo "<p class='correo_usado'>El correo $correo ya está siendo utilizado. Por favor use otro correo</p>";

		} else {

			// Generamos un código de activación

			$string = "";
			$posible = "1234567890abcdefghijklmnopqrstuvwxyz_";
			$i=0;

			while ($i < 20) {
				$char = substr($posible, mt_rand(0, strlen($posible)-1),1);
				$string .= $char;
				$i++;
			}

			$status = 'Disabled';

			$sql_insercion="INSERT INTO usuarios (usuarios, password, correo, codigo_activation, state)
														 VALUES (:usu, :contra, :correo, :c_act, :status)";

			$resultado=$base->prepare($sql_insercion);


			$resultado->execute(array(":usu"=>$usuario, ":contra"=>$pass_cifrado, ":correo"=>$correo, ":c_act"=>$string, ":status"=>$status));


			// Envío de e-mail de confirmación

			$asunto = 'Link de activación de usuario en el Sistema';

			$mensaje = "<html lang = 'es'>"
							 . "<head>"
							 . "<title>Link de Activación de Usuario</title>"
							 . "<meta charset='utf-8' />"
							 . "</head>"
							 . "<body>"
							 . "Gracias por registrarse al Sistema, para poder acceder, debe activar su "
							 . "usuario haciendo click en el siguiente enlace: <br>"
							 . "<a href = 'http://localhost/xampp/encriptaciones/link_activation.php?link=$string'>"
							 . "Activar Cuenta</a>";
			$mensaje .= "</body>"
							 . "</html> ";

		  // Para enviar un correo HTML mail, la cabecera Content-type debe fijarse

			$cabeceras = 'MIME-Version: 1.0' . "\r\n";
			$cabeceras .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

			// Cabeceras adicionales
			$cabeceras .= 'From: System FasTeach <gustavoarmandopc@gmail.com>' . "\r\n";

			// Se hace el envío
			mail($correo, $asunto, $mensaje, $cabeceras);

			echo "Le hemos envíado un correo de confirmación al e-mail registrado";

			$resultado->closeCursor();


		}

	}catch(Exception $e){


		echo "Línea del error: " . $e->getLine();

	}finally{

		$base=null;


	}

?>
</body>
</html>
