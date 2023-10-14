<?php
include_once 'google-api-php-client--PHP7.4/vendor/autoload.php';

 
// variables
$dbhost = 'localhost';
$dbname = 'bdconvencion';
$dbuser = 'elio';
$dbpass = 'Convenio123-';

$backup_file = $dbname. "-" .date("Y-m-d-H-i-s"). ".sql";

// comandos a ejecutar
$commands = array(
        "mysqldump --opt -h $dbhost -u $dbuser -p$dbpass -v $dbname > $backup_file",
      "bzip2 $backup_file"
);

// ejecución y salida de éxito o errores
foreach ( $commands as $command ) {
        system($command,$output);
        echo $output;
        echo "<br><br>";
        echo $backup_file.".bz2";
        echo "<br><br>";
        subirDocumentoDrive($backup_file.".bz2","Copia de seguridad automatico desde el Servidor en fecha ->".date("Y-m-d-H-i-s")."");
}



function subirDocumentoDrive($documento,$descripcion){
    echo "Inicio al subir el Documento Drive<br>";
    // Variables de credenciales.
    $claveJSON = '1MJg0wASBl_umGEO5658v4t_Q--00G8fj';
    $pathJSON = 'temporal-bongo-342614-64928d96d095.json';
    
    //configurar variable de entorno
    putenv('GOOGLE_APPLICATION_CREDENTIALS='.$pathJSON);

    $client = new Google_Client();
    $client->useApplicationDefaultCredentials();
    $client->setScopes(['https://www.googleapis.com/auth/drive.file']);
    try{		
        //instanciamos el servicio
        $service = new Google_Service_Drive($client);

        //instacia de archivo
        $file = new Google_Service_Drive_DriveFile();
        $file->setName($documento);

        //obtenemos el mime type
        //'.bz2', 'application/x-bzip2',
        $finfo = finfo_open('application/x-bzip2'); 
        $mime_type=finfo_file($finfo, $documento);

        //id de la carpeta donde hemos dado el permiso a la cuenta de servicio 
        $file->setParents(array($claveJSON));
        $file->setDescription($descripcion);
        $file->setMimeType($mime_type);
        $result = $service->files->create(
          $file,
          array(
            'data' => file_get_contents($documento),
            'mimeType' => $mime_type,
            'uploadType' => 'media',
          )
        );
        echo "2.- Fichero subido a Google Drive. ";
    }catch(Google_Service_Exception $gs){
        $m=json_decode($gs->getMessage());
        echo "Google_Service_Exception.<br>";
        echo $m->error->message;
    }catch(Exception $e){
        echo "Exception.<br>";
        echo $e->getMessage();  
    }
}
?>
