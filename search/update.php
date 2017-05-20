<?php
include '../config/connect.php';
session_start();
/***************INCREASE THE HITS ***************/
if(isset($_GET['tbl'])&&isset($_GET['id']))
{
    $id=$_GET['id'];
    $table_name=$_GET['tbl'];
    $link=$_GET['lnk'];
    echo $id."<br />".$table_name."<br />".$link;
    $query="select hits from $table_name where link='$link'";
    echo '<br />'.$query.'<br />';
    $result=mysqli_query($con,$query);
    while($hits=  mysqli_fetch_array($result))
        $hit=++$hits['hits'];
    echo $hit;
    $query2="UPDATE  `web_wizard`.`$table_name` SET  `hits` =  '$hit' WHERE  `$table_name`.`link` ='$link'";
    echo $query2;
    if(mysqli_query($con,$query2))
        header("location:$link");
    else
        header("location:index.php");
}
else
{
    header("location:index.php");
}
?>