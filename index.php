<?php
use Phppot\DataSource;

require_once 'DataSource.php';
$db = new DataSource();
$conn = $db->getConnection();

if (isset($_POST["import"])) {
    
    $fileName = $_FILES["file"]["tmp_name"];
    
    if ($_FILES["file"]["size"] > 0) {
        
        $file = fopen($fileName, "r");
        
        while (($column = fgetcsv($file, 10000, "|")) !== FALSE) {
            
            $sku = "";
            if (isset($column[0])) {
                $sku = mysqli_real_escape_string($conn, $column[0]);
            }
            $userName = "";
            if (isset($column[1])) {
                $userName = mysqli_real_escape_string($conn, $column[1]);
            }
            $password = "";
            if (isset($column[2])) {
                $password = mysqli_real_escape_string($conn, $column[2]);
            }
            $firstName = "";
            if (isset($column[3])) {
                $firstName = mysqli_real_escape_string($conn, $column[3]);
            }
            $price = "";
            if (isset($column[4])) {
                $price = mysqli_real_escape_string($conn, $column[4]);
            }
            ////
            $sqlInsert = "UPDATE wps7_wc_product_meta_lookup set min_price = ?, max_price = ? where sku = ?";
            $paramType = "iss";
            $paramArray = array(
                $price,
                $price,
                $sku,
            );
            $update = $db->update($sqlInsert, $paramType, $paramArray);
            

            ////
            $query = "select product_id from productos.wps7_wc_product_meta_lookup where sku = '"."$sku"."'";
            $result = $db->select($query);
            if($result){
                foreach ($result as $row) {
                    $product_id =  $row['product_id'];
                    }
            }
           

            

            ////
            $sqlInsert = "update productos.wps7_postmeta set meta_value = ? where post_id = ? and meta_key IN('_sale_price', '_price');";
            $paramType = "is";
            $paramArray = array(
                $price,
                $product_id,
            );
            $update = $db->update($sqlInsert, $paramType, $paramArray);

            


            if (! empty($update)) {
                $type = "success";
                $message = "CSV ha actualizado todos los precios de los productos";

            } else {
                $type = "error";
                $message = "Problema en la importación del Archivo CSV";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html>

<head>
<script src="jquery-3.2.1.min.js"></script>

<style>
body {
    font-family: Arial;
    width: 704px;
}

.outer-scontainer {
    background: #F0F0F0;
    border: #e0dfdf 1px solid;
    padding: 20px;
    border-radius: 2px;
}

.input-row {
    margin-top: 0px;
    margin-bottom: 20px;
}

.btn-submit {
    background: #333;
    border: #1d1d1d 1px solid;
    color: #f0f0f0;
    font-size: 0.9em;
    width: 100px;
    border-radius: 2px;
    cursor: pointer;
}

.outer-scontainer table {
    border-collapse: collapse;
    width: 100%;
}

.outer-scontainer th {
    border: 1px solid #dddddd;
    padding: 8px;
    text-align: left;
}

.outer-scontainer td {
    border: 1px solid #dddddd;
    padding: 8px;
    text-align: left;
}

#response {
    padding: 10px;
    margin-bottom: 10px;
    border-radius: 2px;
    display: none;
}

.success {
    background: #c7efd9;
    border: #bbe2cd 1px solid;
}

.error {
    background: #fbcfcf;
    border: #f3c6c7 1px solid;
}

div#response.display-block {
    display: block;
}
</style>
<script type="text/javascript">
$(document).ready(function() {
    $("#frmCSVImport").on("submit", function () {

	    $("#response").attr("class", "");
        $("#response").html("");
        var fileType = ".csv";
        var regex = new RegExp("([a-zA-Z0-9\s_\\.\-:])+(" + fileType + ")$");
        if (!regex.test($("#file").val().toLowerCase())) {
        	    $("#response").addClass("error");
        	    $("#response").addClass("display-block");
            $("#response").html("Invalid File. Upload : <b>" + fileType + "</b> Files.");
            return false;
        }
        return true;
    });
});
</script>
</head>

<body>
    <h2>Actualización Precios de Productos</h2>

    <div id="response"
        class="<?php if(!empty($type)) { echo $type . " display-block"; } ?>">
        <?php if(!empty($message)) { echo $message; } ?>
        </div>
    <div class="outer-scontainer">
        <div class="row">

            <form class="form-horizontal" action="" method="post"
                name="frmCSVImport" id="frmCSVImport"
                enctype="multipart/form-data">
                <div class="input-row">
                    <label class="col-md-4 control-label"></label> <input type="file" name="file"
                        id="file" accept=".csv">
                    <button type="submit" id="submit" name="import"
                        class="btn-submit">Importar</button>
                    <br />

                </div>

            </form>

        </div>
               <?php
            /*$sqlSelect = "SELECT * FROM users";
            $result = $db->select($sqlSelect);
            if (! empty($result)) {
                ?>
            <table id='userTable'>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name Product</th>
                    <th>UNID.</th>
                    <th>CANT.</th>
                    <th>Price</th>
                </tr>
            </thead>
<?php
                
                foreach ($result as $row) {
                    ?>
                    
                <tbody>
                <tr>
                    <td><?php  echo $row['id_product']; ?></td>
                    <td><?php  echo $row['name_product']; ?></td>
                    <td><?php  echo $row['uni_product']; ?></td>
                    <td><?php  echo $row['cant_product']; ?></td>
                    <td><?php  echo $row['price_product']; ?></td>
                </tr>
                    <?php
                }
                ?>
                </tbody>
        </table>
        <?php } */?>
    </div>

</body>

</html>