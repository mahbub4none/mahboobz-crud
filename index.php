<form action="" method="post">
    Table Name <input type="text" name="table"/>
    Fields Name <input type="text" name="fields"/>
     <input type="submit" name="submit" value="Submit"/><br>
</form>
"Fields Name should be comma separated like name,email,password ... "
<?php
if($_POST){
    $posted_fields = $_POST['fields'];
    $table = $_POST['table'];

    $fields = explode(',',$posted_fields);
    $fields_post = '';
    $field_count = count($fields);
    $count_field = 0;
    $set_field = '';

    $insert_form = '<form method="post" action="crud_'.$table.'.php">';
    $update_form = '<form method="post" action="crud_'.$table.'.php">';
    $columns = '';
    foreach($fields as $field){
        $columns .= '`'.$field.'` VARCHAR(100) NOT NULL';

        $fields_post .= "'\$_POST[$field]'";//to create post values
        $set_field .= "`$field` = '\$_POST[$field]'";// to create set values

        $insert_form .= $field.' <input type="text" name="'.$field.'"/>
        ';
        $update_form .= $field.' <input type="text" name="'.$field.'" value="<?=$user[\''.$field.'\']?>"/>
        ';

        $count_field++;
        if(!($field_count == $count_field)){
            $columns .= ', ';
            $fields_post .= ', ';
            $set_field .= ', ';
        }
    }
    $insert_form .= '<input type="submit" name="submit" value="Submit"/>
</form>';
    $update_form .= '
    <input type="hidden" name="id" value="<?=$user[\'id\']?>"/>
    <input type="submit" name="update" value="UPDATE"/>
    <input type="submit" name="delete" value="DELETE"/>
</form>';

    $all_sqls = "
    <?php
\$create_sql = \"INSERT into `$table` ($posted_fields) VALUES ($fields_post)\";

\$read_sql = \"SELECT * FROM `$table`\";

\$update_sql = \"UPDATE `$table` SET $set_field WHERE `id`='\$_POST[id]'\";

\$delete_sql = \"DELETE FROM `$table` WHERE `id` = '\$_POST[id]'\";

\$con = mysqli_connect('localhost','root','mysql','crud',3306);

if(\$_POST['submit'])
    mysqli_query(\$con,\$create_sql);

if(\$_POST['update'])
    mysqli_query(\$con,\$update_sql);

if(\$_POST['delete'])
    mysqli_query(\$con,\$delete_sql);

\$read_value = mysqli_query(\$con,\$read_sql);
?>

<?php while(\$user = mysqli_fetch_array(\$read_value)){?>
$update_form
<?}?>
";

    //echo $all_sqls;

    $table_query = "CREATE TABLE `$table` ( `id` INT NOT NULL AUTO_INCREMENT , ".$columns." , PRIMARY KEY (`id`) ) ENGINE = InnoDB;";

    $con = mysqli_connect('localhost','root','mysql','crud',3306);
    mysqli_query($con,$table_query);

    $directory = 'crud_'.$table.'.php';
    $file = fopen($directory,"w");

    $data = $insert_form.$all_sqls;
    fwrite($file,$data);
    fclose($file);
}
