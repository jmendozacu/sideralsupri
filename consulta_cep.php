<?php

$cep = substr($_GET['cep'], 0, 5)."-".substr($_GET['cep'], 5, 7);

$servername = "mysql.mucci.local";
$username = "ashop";
$password = "fjqvho/61Wmj";
$dbname = "ashop_database4";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql  = "SELECT * FROM `enderecos` WHERE `cep` LIKE '".$cep."'";

$result = $conn->query($sql);

if ($result->num_rows > 0) {

    while($row = $result->fetch_assoc()) { ?>
        var resultadoCEP = { 
            'uf' : '<?php echo utf8_encode($row["uf"]); ?>', 
            'cidade' : '<?php echo utf8_encode($row["cidade"]); ?>', 
            'bairro' : '<?php echo utf8_encode($row["bairro"]); ?>', 
            'tipo_logradouro' : '',
            'logradouro' : '<?php echo utf8_encode($row["logradouro"]); ?>', 
            'resultado' : '1', 
            'resultado_txt' : 'sucesso - cep completo' 
        }
    
<?php

    }
}

$conn->close(); 

?>