<?php
include "config.php";
include "utils.php";


$dbConn =  connect($db);

/*
  listar todos los posts o solo uno
 */
if ($_SERVER['REQUEST_METHOD'] == 'GET')
{
      
  if (isset($_GET['id_biblioteca']))
  {
    $sql = $dbConn->prepare("SELECT * FROM biblioteca where id_biblioteca=:id_biblioteca");
    $sql->bindValue(':id_biblioteca', $_GET['id_biblioteca']);
    $sql->execute();
    $row_count =$sql->fetchColumn();
    if ($row_count==0) {
      header("HTTP/1.1 204 No Content");
      echo "No existe el registro ",$_GET['id_biblioteca'];
      
    }else{

      echo "Si existe el registro";
      $sql = $dbConn->prepare("SELECT * FROM biblioteca where id_biblioteca=:id_biblioteca");
      $sql->bindValue(':id_biblioteca', $_GET['id_biblioteca']);
      $sql->execute();
      header("HTTP/1.1 200 OK");
      echo json_encode(  $sql->fetch(PDO::FETCH_ASSOC)  );
      exit();
    }

  }
  else {
    if(isset($_GET['nombre_biblioteca'])){
      $sql = $dbConn->prepare("SELECT * FROM biblioteca where nombre_biblioteca=:nombre_biblioteca");
      $sql->bindValue(':nombre_biblioteca', $_GET['nombre_biblioteca']);
      $sql->execute();
      $row_count =$sql->fetchColumn();
      
      if ($row_count==0) {
        header("HTTP/1.1 204 No Content");
        echo "No existe el registro ",$_GET['nombre_biblioteca'];
        
      }else{
        
        echo "Si existe el registro";
        
        $sql = $dbConn->prepare("SELECT biblioteca.nombre_biblioteca, biblioteca.provincia_biblioteca, libros.nombre_libro, libros.autor FROM biblioteca JOIN libros ON biblioteca.id_biblioteca = libros.biblioteca_id  where nombre_biblioteca=:nombre_biblioteca");
        $sql->bindValue(':nombre_biblioteca', $_GET['nombre_biblioteca']);
        $sql->execute();
        header("HTTP/1.1 200 OK");
        // echo json_encode(  $sql->fetch(PDO::FETCH_ASSOC)  );
        echo json_encode( $sql->fetchAll(PDO::FETCH_ASSOC)  );
        exit();
      }

    }else{
      //Mostrar lista de post
      $sql = $dbConn->prepare("SELECT * FROM biblioteca");
      $sql->execute();
      $sql->setFetchMode(PDO::FETCH_ASSOC);
      header("HTTP/1.1 200 OK");
      echo json_encode( $sql->fetchAll()  );
      exit();
    }
  }

}

// Crear un nuevo post
if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
  if (isset($_POST['nombre_biblioteca'])){
    $sql = $dbConn->prepare("SELECT * FROM biblioteca where nombre_biblioteca=:nombre_biblioteca");
    $sql->bindValue(':nombre_biblioteca', $_POST['nombre_biblioteca']);
    $sql->execute();
    $row_count =$sql->fetchColumn();
    if ($row_count>0) {
      header("HTTP/1.1 204 No Content");
      echo "Ya existe la biblioteca ", $_POST['nombre_biblioteca'];
    }else{
      echo "Guardado Exitosamente";
      $input = $_POST;
      $sql = "INSERT INTO biblioteca
            (nombre_biblioteca, direccion_biblioteca, provincia_biblioteca, ciudad_biblioteca, telefono_biblioteca)
            VALUES
            (:nombre_biblioteca, :direccion_biblioteca, :provincia_biblioteca, :ciudad_biblioteca, :telefono_biblioteca)";
      $statement = $dbConn->prepare($sql);
      bindAllValues($statement, $input);
      $statement->execute();
      $postId = $dbConn->lastInsertId();
      if($postId)
      {
        $input['id_biblioteca'] = $postId;
        header("HTTP/1.1 200 OK");
        echo json_encode($input);
        exit();
  	 }
    }
  }else{
    echo "EL campo codigo es obligatorio";
  }

}

//Borrar
if ($_SERVER['REQUEST_METHOD'] == 'DELETE')
{
  if (isset($_GET['nombre_biblioteca'])){
  	// $id = $_GET['id'];
    $sql = $dbConn->prepare("SELECT COUNT(*) FROM biblioteca where nombre_biblioteca=:nombre_biblioteca");
    $sql->bindValue(':nombre_biblioteca', $_GET['nombre_biblioteca']);
    $sql->execute();
    $row_count =$sql->fetchColumn();
    // echo $row_count;
    if ($row_count == 0) {
      echo "No existe el registro ",$_GET['nombre_biblioteca'];
      header("HTTP/1.1 400 Bad Request"); //error 400 por no ejecutar el delete

    }else{
      $nombre_biblioteca = $_GET['nombre_biblioteca'];
      $statement = $dbConn->prepare("DELETE FROM biblioteca where nombre_biblioteca=:nombre_biblioteca");
      $statement->bindValue(':nombre_biblioteca', $nombre_biblioteca);
      $statement->execute();
      echo "Eliminado la biblioteca ",$_GET['nombre_biblioteca'];
    	header("HTTP/1.1 200 OK");
    	exit();
    }
  }else{
    echo "El parametro nombre es obligatorio";
  }


}

//Actualizar
if ($_SERVER['REQUEST_METHOD'] == 'PUT')
{


  if (isset($_GET['nombre_biblioteca'])){
    $sql = $dbConn->prepare("SELECT * FROM biblioteca where nombre_biblioteca=:nombre_biblioteca");
    $sql->bindValue(':nombre_biblioteca', $_GET['nombre_biblioteca']);
    $sql->execute();
    $row_count =$sql->fetchColumn();
    if ($row_count>0) {
      $input = $_GET;
      $nombre_biblioteca = $input['nombre_biblioteca'];
      $fields = getParams($input);

      $sql = "
            UPDATE biblioteca
            SET $fields
            WHERE nombre_biblioteca='$nombre_biblioteca'
             ";

      $statement = $dbConn->prepare($sql);
      bindAllValues($statement, $input);

      $statement->execute();
      header("HTTP/1.1 200 OK");
      echo "Actualizado Exitosamente la biblioteca ", $_GET['nombre_biblioteca'];
      exit();
    }else{
      header("HTTP/1.1 204 No Content");
      echo "No existe el la biblioteca ", $_GET['nombre_biblioteca'];
    }
  }else{
    echo "El parametro codigo es obligatorio";
  }
}


//En caso de que ninguna de las opciones anteriores se haya ejecutado
header("HTTP/1.1 400 Bad Request");

?>