<!DOCTYPE html>
<html>
    <head>
        <title>INDEXING</title>
        <link rel="stylesheet" type="text/css" href="../css/main.css" />
        <link rel="stylesheet" type="text/css" href="../css/style2.css" />
    </head>
    <body>
        <header>
            <div class="container">
                <div class="vert_mid">
                    <div id='left'><a href="../indec.php">Web Wizard</a></div>
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
        <article id="article3" class="container">
            <div class="vert_mid">
        <?php
    include '../inc/porter_stemming_algo.php';
    include '../config/connect.php';
    include '../inc/stop_words.php';
    include '../inc/simple_html_dom.php';
    function crawl_content($link)
    {
        include '../config/connect.php';
        if (!(@fopen($link,'r'))){
            die('Not a valid URL');
        }
        else
        {
            $tags = get_meta_tags($link);
            if(isset($tags['description']))
                $desc=  strtolower ($tags['description']);
            else
                $desc='';
            echo '<table>';
            echo '<tr><th>Keyword</th><th>link</th><th>Description</th><th>Hits</th><th>Priority</th></tr>';
            /*********************TITLE************/
            $html = new simple_html_dom();
            $html->load_file($link);
            if($html->find('title',0)->plaintext)
            {
                $real_title=$html->find('title',0)->plaintext;
                if($html->find('title',0)->plaintext)
                {
                    $title=strtolower($html->find('title',0)->plaintext);
                    $priority=1;
                    while(strpos($title," ",0)===0)
                        $title=substr_replace($title,"",0,1);
                    if(strpos($title," ")===false)
                    {
                        $title_first_letter=substr($title,0,1);
                        $table_name="links_".$title_first_letter;
                        $title_port=PorterStemmer::Stem($title);
                        if($title_port!==$title)
                        {
                            $query_title1="INSERT INTO `web_wizard`.`$table_name` (`keyword`, `link`, `desc`,`hits`,`priority`,`title`) VALUES ('$title_port', '$link', '$desc','0','$priority','$real_title')";
                            $query_title2="INSERT INTO `web_wizard`.`$table_name` (`keyword`, `link`, `desc`,`hits`,`priority`,`title`) VALUES ('$title', '$link', '$desc','0','$priority','$real_title')";
                            if(mysqli_query($con,$query_title1))
                                echo '<tr><td>'.$title_port.'</td><td>'.$link.'</td><td>'.$desc.'</td><td>0</td><td>'.$priority.'</td></tr>';
                            if(mysqli_query($con,$query_title2))
                                echo '<tr><td>'.$title.'</td><td>'.$link.'</td><td>'.$desc.'</td><td>0</td><td>'.$priority.'</td></tr>';
                        }
                        else
                        {
                            $query_title3="INSERT INTO `web_wizard`.`$table_name` (`keyword`, `link`, `desc`,`hits`,`priority`,`title`) VALUES ('$title', '$link', '$desc','0','$priority','$real_title')";
                            if(mysqli_query($con,$query_title3))
                                echo '<tr><td>'.$title.'</td><td>'.$link.'</td><td>'.$desc.'</td><td>0</td><td>'.$priority.'</td></tr>';
                        }
                    }
                    else
                    {
                        $main_title=$title;
                        $main_title_port= removeCommonWords($main_title);
                        $title_keyword = explode(" ",$main_title_port);
                        array_push($title_keyword,$main_title,$main_title_port);
                        foreach($title_keyword as $key)
                        {
                            $title_first_letter=substr($key,0,1);
                            $table_name="links_".$title_first_letter;
                            if(strpos($key," ")===false)
                            {    
                                $title_port=PorterStemmer::Stem($key);
                                if($title_port!==$key)
                                {
                                    $query_title4="INSERT INTO `web_wizard`.`$table_name` (`keyword`, `link`, `desc`,`hits`,`priority`,`title`) VALUES ('$title_port', '$link', '$desc','0','$priority','$real_title')";
                                    $query_title5="INSERT INTO `web_wizard`.`$table_name` (`keyword`, `link`, `desc`,`hits`,`priority`,`title`) VALUES ('$key', '$link', '$desc','0','$priority','$real_title')";
                                    if(mysqli_query($con,$query_title4))
                                        echo '<tr><td>'.$title_port.'</td><td>'.$link.'</td><td>'.$desc.'</td><td>0</td><td>'.$priority.'</td></tr>';
                                    if(mysqli_query($con,$query_title5))
                                        echo '<tr><td>'.$key.'</td><td>'.$link.'</td><td>'.$desc.'</td><td>0</td><td>'.$priority.'</td></tr>';
                                }
                                else
                                {
                                    $query_title6="INSERT INTO `web_wizard`.`$table_name` (`keyword`, `link`, `desc`,`hits`,`priority`,`title`) VALUES ('$key', '$link', '$desc','0','$priority','$real_title')";
                                    if(mysqli_query($con,$query_title6))
                                        echo '<tr><td>'.$key.'</td><td>'.$link.'</td><td>'.$desc.'</td><td>0</td><td>'.$priority.'</td></tr>';
                                }
                            }
                            else
                            {
                                $query_title7="INSERT INTO `web_wizard`.`$table_name` (`keyword`, `link`, `desc`,`hits`,`priority`,`title`) VALUES ('$key', '$link', '$desc','0','$priority','$real_title')";
                                if(mysqli_query($con,$query_title7))
                                    echo '<tr><td>'.$key.'</td><td>'.$link.'</td><td>'.$desc.'</td><td>0</td><td>'.$priority.'</td></tr>';
                            }
                        }
                    }
                }
                if(isset($tags['keywords']))
                {
				
                $keywords = explode(',',strtolower( $tags['keywords']));
                $priority=0;
                foreach($keywords as $keyword)
                {
                    while(strpos($keyword," ",0)===0)
                        $keyword=substr_replace($keyword,"",0,1);
                    if(strpos($keyword," ")===false)
                    {
                        $keyword_first_letter=substr($keyword,0,1);
                        $table_name="links_".$keyword_first_letter;
                        $keyword_port=PorterStemmer::Stem($keyword);
                        if($keyword_port!==$keyword)
                        {
                            $query_keyword1="INSERT INTO `web_wizard`.`$table_name` (`keyword`, `link`, `desc`,`hits`,`priority`,`title`) VALUES ('$keyword_port', '$link', '$desc','0','$priority','$real_title')";
                            $query_keyword2="INSERT INTO `web_wizard`.`$table_name` (`keyword`, `link`, `desc`,`hits`,`priority`,`title`) VALUES ('$keyword', '$link', '$desc','0','$priority','$real_title')";
                            if(mysqli_query($con,$query_keyword1))
                                echo '<tr><td>'.$keyword_port.'</td><td>'.$link.'</td><td>'.$desc.'</td><td>0</td><td>'.$priority.'</td></tr>';
                            if(mysqli_query($con,$query_keyword2))
                                echo '<tr><td>'.$keyword.'</td><td>'.$link.'</td><td>'.$desc.'</td><td>0</td><td>'.$priority.'</td></tr>';
                        }
                        else
                        {
                            $query_keyword3="INSERT INTO `web_wizard`.`$table_name` (`keyword`, `link`, `desc`,`hits`,`priority`,`title`) VALUES ('$keyword', '$link', '$desc','0','$priority','$real_title')";
                            if(mysqli_query($con,$query_keyword3))
                                echo '<tr><td>'.$keyword.'</td><td>'.$link.'</td><td>'.$desc.'</td><td>0</td><td>'.$priority.'</td></tr>';
                        }
                    }
                    else
                    {
                        $main_keyword=$keyword;
                        $main_keyword_port= removeCommonWords($main_keyword);
                        $keyword_keyword = explode(" ",$main_keyword_port);
                        array_push($keyword_keyword,$main_keyword,$main_keyword_port);
                        foreach($keyword_keyword as $key)
                        {
                            if(strpos($key," ")===false)
                            {
                                $keyword_first_letter=substr($key,0,1);
                                $table_name="links_".$keyword_first_letter;
                                $keyword_port=PorterStemmer::Stem($key);
                                if($keyword_port!==$key)
                                {
                                    $query_keyword4="INSERT INTO `web_wizard`.`$table_name` (`keyword`, `link`, `desc`,`hits`,`priority`,`title`) VALUES ('$keyword_port', '$link', '$desc','0','$priority','$real_title')";
                                    $query_keyword5="INSERT INTO `web_wizard`.`$table_name` (`keyword`, `link`, `desc`,`hits`,`priority`,`title`) VALUES ('$key', '$link', '$desc','0','$priority','$real_title')";
                                    if(mysqli_query($con,$query_keyword4))
                                        echo '<tr><td>'.$keyword_port.'</td><td>'.$link.'</td><td>'.$desc.'</td><td>0</td><td>'.$priority.'</td></tr>';
                                    if(mysqli_query($con,$query_keyword5))
                                        echo '<tr><td>'.$key.'</td><td>'.$link.'</td><td>'.$desc.'</td><td>0</td><td>'.$priority.'</td></tr>';
                                }
                                else
                                {
                                    $query_keyword6="INSERT INTO `web_wizard`.`$table_name` (`keyword`, `link`, `desc`,`hits`,`priority`,`title`) VALUES ('$keyword', '$link', '$desc','0','$priority','$real_title')";
                                    if(mysqli_query($con,$query_keyword6))
                                        echo '<tr><td>'.$key.'</td><td>'.$link.'</td><td>'.$desc.'</td><td>0</td><td>'.$priority.'</td></tr>';
                                }
                            }
                            else
                            {
                                $query_keyword7="INSERT INTO `web_wizard`.`$table_name` (`keyword`, `link`, `desc`,`hits`,`priority`,`title`) VALUES ('$keyword', '$link', '$desc','0','$priority','$real_title')";
                                if(mysqli_query($con,$query_keyword7))
                                    echo '<tr><td>'.$keyword.'</td><td>'.$link.'</td><td>'.$desc.'</td><td>0</td><td>'.$priority.'</td></tr>';
                            }
                        }
                    }
                    }    
            }
            }
            echo '</table>';
        }
        return true;
    }
    while(1)
    {
        $get_query="select * from temp_link where status=0 limit 0,1";
        $get_result=mysqli_query($con,$get_query);
        if(mysqli_num_rows($get_result)===0)
            break;
        else
        {
            foreach($get_result as $get_link)
            {
                echo "<h1>getting details of <a href='".$get_link['link']."'>".$get_link['link']."</a></h1>";
                if(crawl_content($get_link['link']))
                {
                    $link2=$get_link['link'];
                    $update_query="UPDATE  `web_wizard`.`temp_link` SET  `status` =  '1' WHERE  `temp_link`.`link` ='".$link2."'";
                    mysqli_query($con,$update_query);
                }
            }
        }
    }
?>
            </div>
        </article>
    </body>
</html>