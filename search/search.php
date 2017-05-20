<!DOCTYPE html>
<html>
    <head>
        <title>SEARCH</title>
        <link rel="stylesheet" type="text/css" href="../css/main.css" />
        <link rel="stylesheet" type="text/css" href="../css/style2.css" />
        <style>
            .keyword a{
                border-bottom:2px solid grey;
            }
            .result:hover,.result:hover a{   
                color:#3c3;
            }
            .result:hover .keyword a{
                border-color:#3c3;
            }
        </style>
    </head>
    <body>
        <header>
            <div class="container">
                <div class="vert_mid">
                    <div id='left'><a href="../index.php">Web Wizard</a></div>
                <?php
                    session_start();
                    if(isset($_SESSION['loggedin']))
                    {
                        $f_name=$_SESSION['f_name'];
                        $l_name=$_SESSION['l_name'];
                        $m_name=$_SESSION['m_name'];
                        
                        echo "<div id='right'><a href='../user/user_panel.php'>Hi, $f_name $m_name $l_name</a>&nbsp;&nbsp;";
                        echo "<a href='../user/logout.php'>Logout</a></div>";
                    }
                    else
                    {
                        echo "<a id='right' href='../user/index.php'>Login/Signup</a>";
                    }
                ?>
                    </div>
                </div>
        </header>
        <article id="article2" class="container">
            <div class="vert_mid">
                <form action="search.php" method="get">
                    <input type="text" name="search_string" /><br />
                    <input type="submit" value="Search" />
                </form>
            </div>
        </article>
        <article id="article3" class="container">
            <div class="vert_mid">
    <?php
    if(isset($_GET['search_string']))
    {
        include '../config/connect.php';
        include '../inc/porter_stemming_algo.php';
        include '../inc/simple_html_dom.php';
        include '../inc/stop_words.php';
        if(isset($_GET['page']))
        {
            $page=$_GET['page'];
        }
        else
        {
            $page=1;
        }
    /**************GIVES ALL POSSIBLE COMBINATION OF WORDS FROM STRING****************/
        function depth_picker($arr, $temp_string, &$collect) {
            if ($temp_string != "") 
                $collect []= $temp_string;
            for ($i=0; $i<sizeof($arr);$i++) {
                $arrcopy = $arr;
                $elem = array_splice($arrcopy, $i, 1); // removes and returns the i'th element
                if (sizeof($arrcopy) > 0) {
                    depth_picker($arrcopy, $temp_string ." " . $elem[0], $collect);
                } else {
                    $collect []= $temp_string. " " . $elem[0];
                }   
            }   
        }
    /************************SORT STRING ACOORDING TO THEIR LENGTH********************/
        function sort_array($a,$b){
            return strlen($b)-strlen($a);
        }
    /*******************SEARCH A QUERY GIVEN BY THE USER*****************************/
        $search_string =$_GET['search_string'];
        echo "<h1>You Have Searched for '$search_string'</h1>";
        if(isset($_SESSION['loggedin']))
        {
            $user=$_SESSION['user'].".csv";
            $insert_string=array($search_string);
            $check_count=0; 
            $handle = fopen("../user/user_history/".$user,"r");
            while (($data = fgetcsv($handle,"", ",")) !== FALSE) 
            {
                $blackpowder = $data;
                $dynamit = implode(";", $blackpowder);
                $pieces = explode(";", $dynamit);
                $col1 = $pieces[0];
                if(strtolower($col1)==strtolower($search_string))
                {
                    $check_count=1;
                }
            }
            if($check_count==0)
            {
                $fp = fopen('../user/user_history/'.$user, 'a');
                fputcsv($fp, $insert_string, "\t", '"');
                fclose($fp);
            }
            
        }
        if(strlen($search_string)!==1)
            $remove_common_string=  removeCommonWords($search_string);
        else
            $remove_common_string=$search_string;
        $explode_string=  explode(" ", $remove_common_string);
        $explode_string=array_filter($explode_string);
        $collect = array();
        depth_picker($explode_string, "", $collect);
        usort($collect,"sort_array");
        foreach($collect as $arr){
            $arr=substr_replace($arr,"",0,1);
            $arr_port=  PorterStemmer::Stem($arr);
            $key_first=substr($arr,0,1);
            $table_name="links_".$key_first;
            $query="select distinct(`link`),`title`,`desc` from `web_wizard`.`".$table_name."` where `keyword` LIKE '%".$arr."%' OR `keyword` LIKE '%".$arr_port."%' ORDER BY  `".$table_name."`.`priority` DESC, `".$table_name."`.`hits` DESC";
            $result=  mysqli_query($con, $query);
            $count_row=mysqli_num_rows($result);
            if($count_row!=0)
            {
                $bullet_count=5;
                $count_key=10;
                $lower_limit=$count_key*($page-1);
                if(($page)>=($count_row/$count_key))
                    $upper_limit=$count_row;
                else 
                    $upper_limit=$count_key*($page);
                echo "<div id='show'>showing ".($lower_limit+1)." - $upper_limit out of $count_row results</div>";
                echo '<table>';
                $i=0;
                while ($row=  mysqli_fetch_array($result))
                {
                    if(($i>=$lower_limit)&&$i<($upper_limit))
                    {
                        $arr_result=$row['title'];
                        echo "<div class='result'>";
                        echo "<div class='keyword' ><a  href='update.php?id=".$row['link']."&&tbl=$table_name&&lnk=".$row['link']."'>".$arr_result."</a></div>";
                        echo "<div class='link'><a class='link' href='update.php?id=".$row['link']."&&tbl=$table_name&&lnk=".$row['link']."'>".$row['link']."</a></div>";
                        echo "<div class='desc'>".$row['desc']."</div>";
                        echo '</div>';
                    }
                    if($i<=($count_key*$page))
                        $i++;
                    else
                         break;
                }
                $j=1;
                echo '<div id="bullets">';
                
                if($page<=1)
                {
                    echo "<a class='disable'><<</a>";
                    echo "<a class='disable'><</a>";
                }
                else
                {
                    $prev=$page-1;
                    echo "<a href='search.php?search_string=$search_string&&page=1'><<</a>";
                    echo "<a href='search.php?search_string=$search_string&&page=$prev'><</a>";
                }
                if(!($page-1<=1))
                {
                    $prev=$page-2;
                    echo "<a href='search.php?search_string=$search_string&&page=$prev'>$prev</a>";
                }
                if(!($page<=1))
                {
                    $prev=$page-1;
                    echo "<a href='search.php?search_string=$search_string&&page=$prev'>$prev</a>";
                }
                echo "<a  class='disable'>$page</a>";
                $last_page=  ceil($count_row/$count_key);
                if(!($page>=$last_page))
                {
                    $next=$page+1;
                    echo "<a href='search.php?search_string=$search_string&&page=$next'>$next</a>";
                }
                if(!($page+1>=$last_page))
                {
                    $next=$page+2;
                    echo "<a href='search.php?search_string=$search_string&&page=$next'>$next</a>";
                }
                $last_page=  ceil($count_row/$count_key);
                if($page>=$last_page)
                {
                    echo "<a class='disable'>></a>";
                    echo "<a class='disable'>>></a>";
                }
                else
                {
                    $next=$page+1;
                    echo "<a href='search.php?search_string=$search_string&&page=$next'>></a>";
                    echo "<a href='search.php?search_string=$search_string&&page=$last_page'>>></a>";
                }
                echo "</div>";
            }
            else
            {
                echo 'No Results Found on Database';
            }
        }
    }
    else 
    {
        header('location:../index.php');
    }
?>
            </div></article>
                <footer></footer>
            </html>