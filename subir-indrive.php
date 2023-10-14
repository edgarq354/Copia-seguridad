<?php
include_once 'google-api-php-client--PHP7.4/vendor/autoload.php';

 

function subirDocumentoDrive($documento,$descripcion){
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
        echo "2.- Fichero subido a Google Drive. 
";
    }catch(Google_Service_Exception $gs){
        $m=json_decode($gs->getMessage());
        echo $m->error->message;
    }catch(Exception $e){
        echo $e->getMessage();  
    }
}
?>