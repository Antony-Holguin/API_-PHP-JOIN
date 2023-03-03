<?php
include "config.php";
include "utils.php";


$dbConn =  connect($db);

/*
  listar todos los posts o solo uno
 */
if ($_SERVER['REQUEST_METHOD'] == 'GET')
{
      
  if (isset($_GET['id_libros']))
  {
    $sql = $dbConn->prepare("SELECT * FROM libros where id_libros=:id_libros");
    $sql->bindValue(':id_libros', $_GET['id_libros']);
    $sql->execute();
    $row_count =$sql->fetchColumn();
    if ($row_count==0) {
      header("HTTP/1.1 204 No Content");
      echo "No existe el libro ",$_GET['id_libros'];
      
    }else{
      
      echo "Si existe el registro";
      $sql = $dbConn->prepare("SELECT * FROM libros where id_libros=:id_libros");
      $sql->bindValue(':id_libros', $_GET['id_libros']);
      $sql->execute();
      header("HTTP/1.1 200 OK");
      echo json_encode(  $sql->fetch(PDO::FETCH_ASSOC)  );
      exit();
    }
    
  }else {
    if(isset($_GET['nombre_libro'])){
      $sql = $dbConn->prepare("SELECT * FROM libros where nombre_libro=:nombre_libro");
      $sql->bindValue(':nombre_libro', $_GET['nombre_libro']);
      $sql->execute();
      $row_count =$sql->fetchColumn();
      
      if ($row_count==0) {
        header("HTTP/1.1 204 No Content");
        echo "No existe el registro ",$_GET['nombre_libro'];
        
      }else{
        
        echo "Si existe el registro";
        //------------------------------------OJO
        $sql = $dbConn->prepare("SELECT * FROM libros JOIN biblioteca ON libros.biblioteca_id  = biblioteca.id_biblioteca   where nombre_libro=:nombre_libro");
        $sql->bindValue(':nombre_libro', $_GET['nombre_libro']);
        $sql->execute();
        header("HTTP/1.1 200 OK");
        // echo json_encode(  $sql->fetch(PDO::FETCH_ASSOC)  );
        echo json_encode( $sql->fetchAll(PDO::FETCH_ASSOC)  );
        exit();
      }

    }else{
      if(isset($_GET['isbn_libro'])){
        $sql = $dbConn->prepare("SELECT * FROM libros where isbn_libro=:isbn_libro");
        $sql->bindValue(':isbn_libro', $_GET['isbn_libro']);
        $sql->execute();
        $row_count =$sql->fetchColumn();
        
        if ($row_count==0) {
          header("HTTP/1.1 204 No Content");
          echo "No existe el ISBN ",$_GET['isbn_libro'];
          
        }else{
          
          echo "Si existe el registro";
          
          $sql = $dbConn->prepare("SELECT * FROM libros JOIN biblioteca ON biblioteca.id_biblioteca = libros.biblioteca_id  where isbn_libro=:isbn_libro");
          $sql->bindValue(':isbn_libro', $_GET['isbn_libro']);
          $sql->execute();
          header("HTTP/1.1 200 OK");
          // echo json_encode(  $sql->fetch(PDO::FETCH_ASSOC)  );
          echo json_encode( $sql->fetchAll(PDO::FETCH_ASSOC)  );
          exit();
        }
  
      }else{

        //Mostrar lista de post
        $sql = $dbConn->prepare("SELECT * FROM libros");
        
        $sql->execute();
        $sql->setFetchMode(PDO::FETCH_ASSOC);
        header("HTTP/1.1 200 OK");
        echo json_encode( $sql->fetchAll()  );
        exit();
      }

    }
  }

}

// Crear un nuevo post
if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
  if (isset($_POST['nombre_libro'])){
    $sql = $dbConn->prepare("SELECT * FROM libros where nombre_libro=:nombre_libro");
    $sql->bindValue(':nombre_libro', $_POST['nombre_libro']);
    $sql->execute();
    $row_count =$sql->fetchColumn();
    if ($row_count>0) {
      header("HTTP/1.1 204 No Content");
      echo "Ya existe el libro ", $_POST['nombre_libro'];
    }else{
      echo "Guardado Exitosamente";
      $input = $_POST;
      $sql = "INSERT INTO libros
            (nombre_libro, isbn_libro, autor, email, biblioteca_id)
            VALUES
            (:nombre_libro, :isbn_libro, :autor, :email, :biblioteca_id)";
      $statement = $dbConn->prepare($sql);
      bindAllValues($statement, $input);
      $statement->execute();
      $postId = $dbConn->lastInsertId();
      if($postId)
      {
        $input['id_libros'] = $postId;
        header("HTTP/1.1 200 OK");
        echo json_encode($input);
        exit();
  	 }
    }
  }else{
    echo "EL campo nombre libro es obligatorio";
  }

}

//Borrar
if ($_SERVER['REQUEST_METHOD'] == 'DELETE')
{
  if (isset($_GET['nombre_libro'])){
  	// $id = $_GET['id'];
    $sql = $dbConn->prepare("SELECT COUNT(*) FROM libros where nombre_libro=:nombre_libro");
    $sql->bindValue(':nombre_libro', $_GET['nombre_libro']);
    $sql->execute();
    $row_count =$sql->fetchColumn();
    // echo $row_count;
    if ($row_count == 0) {
      echo "No existe el registro ",$_GET['nombre_libro'];
      header("HTTP/1.1 400 Bad Request"); //error 400 por no ejecutar el delete

    }else{
      $nombre_libro = $_GET['nombre_libro'];
      $statement = $dbConn->prepare("DELETE FROM libros where nombre_libro=:nombre_libro");
      $statement->bindValue(':nombre_libro', $nombre_libro);
      $statement->execute();
      echo "Eliminado el registro ",$_GET['nombre_libro'];
    	header("HTTP/1.1 200 OK");
    	exit();
    }
  }else{
    echo "El parametro nombre libro es obligatorio";
  }


}

//Actualizar
if ($_SERVER['REQUEST_METHOD'] == 'PUT')
{


  if (isset($_GET['nombre_libro'])){
    $sql = $dbConn->prepare("SELECT * FROM libros where nombre_libro=:nombre_libro");
    $sql->bindValue(':nombre_libro', $_GET['nombre_libro']);
    $sql->execute();
    $row_count =$sql->fetchColumn();
    if ($row_count>0) {
      $input = $_GET;
      $postId = $input['nombre_libro'];
      $fields = getParams($input);

      $sql = "
            UPDATE libros
            SET $fields
            WHERE nombre_libro='$postId'
             ";

      $statement = $dbConn->prepare($sql);
      bindAllValues($statement, $input);

      $statement->execute();
      header("HTTP/1.1 200 OK");
      echo "Actualizado Exitosamente el libro ", $_GET['nombre_libro'];
      exit();
    }else{
      header("HTTP/1.1 204 No Content");
      echo "No existe el libro ", $_GET['nombre_libro'];
    }
  }else{
    echo "El parametro nombre libro es obligatorio";
  }
}


//En caso de que ninguna de las opciones anteriores se haya ejecutado
header("HTTP/1.1 400 Bad Request");

?>