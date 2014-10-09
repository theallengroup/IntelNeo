<?php

//==============================================================================
//Funcion que recibe el archivo del post para leerlo
//==============================================================================
    $data = $_POST['img'];//file_get_contents("php://input");
    //var_dump($_POST['img']); exit;
    $filteredData=substr($data, strpos($data, ",")+1);//Filtra el string y extrae los datos solo de la imagen
    $decodedData=base64_decode($filteredData);//Decodifica la imagen que se encuentra en base64

    $fic_name = 'snapshot'.rand(1000,9999).'.png';//Generamos un nombre para la imagen
    //$_SESSION['image_path'] = '../images_photo_chose/'.$fic_name;
    $_SESSION['img_path'] = $fic_name;
    $fp = fopen('../images_photo_chose/'.$fic_name, 'wb');//Generamos la ruta, el nombre y los permisos 
    $ok = fwrite( $fp, $decodedData);//Guardamos la imagen en la ruta con el nombre
    fclose( $fp );//Cerramos la funcion
    
    header('Content-Type: application/json');
    $return = array('image_path' => $fic_name);
    echo json_encode($return);  exit;
    /*if($ok)//verificamos el estado, si guardo o no
    {
		echo '../images_photo_chose/'.$fic_name;
	}
    else
	{
        echo "ERROR";
	}*/
	
?>