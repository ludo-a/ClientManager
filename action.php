<?php
$connect = new PDO('mysql:host=localhost;dbname=clientmanager;charset=utf8', 'root', 'root');
$received_data = json_decode(file_get_contents("php://input"));
$data = array();

if($received_data->action == 'fetchall')
{
    $query = "SELECT * FROM users ORDER BY id DESC";
    $statement = $connect->prepare($query);
    $statement->execute();
    while($row = $statement->fetch(PDO::FETCH_ASSOC))
    {
        $data[] = $row;
    }
    echo json_encode($data);
}

if($received_data->action == 'insert')
{
    $data = array(
    ':name' =>$received_data->name,
    ':surname' =>$received_data->surname,
    ':subscribe'=>$received_data->subscribe,
    ':boxnumber'=>$received_data->boxnumber
    );

    $query = "INSERT INTO users (name, surname, subscribe, boxnumber) VALUES (:name, :surname, :subscribe, :boxnumber)";
    $statement = $connect->prepare($query);
    $statement->execute($data);
    $output = array(
        'message' => 'Client Ajouté'
    );
    echo json_encode($output);
}

if($received_data->action == 'delete')
{
    $query = "DELETE FROM users WHERE id = '".$received_data->id."'";
    $statement = $connect->prepare($query);
    $statement->execute();
    $output = array(
        'message' => 'Client Supprimé'
    );
    echo json_encode($output);
}

if($received_data->action == 'search')
{
    if($received_data->query != '')
    {
        $query = "SELECT * FROM users WHERE name LIKE '%".$received_data->query."%' OR surname LIKE '%".$received_data->query."%' ORDER BY id DESC";
    }
    else
    {
        $query = "SELECT * FROM users ORDER BY id DESC";
    }
        $statement = $connect->prepare($query);
        $statement->execute();
        while($row = $statement->fetch(PDO::FETCH_ASSOC))
    {
        $data[] = $row;
    }
    echo json_encode($data);
}

if($received_data->action == 'update')
{
    $data = array(
    ':boxnumber' => $received_data->boxnumber,
    ':id'   => $received_data->hiddenId
    );

    $query = "UPDATE users SET boxnumber = :boxnumber WHERE id = :id";
    $statement = $connect->prepare($query);
    $statement->execute($data);
    $output = array(
    'message' => 'Client MIS A JOUR'
    );
    echo json_encode($output);
}
?>