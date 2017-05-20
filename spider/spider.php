<!DOCTYPE html>
<html>
    <head>
        <title>CRAWLLING</title>
        <link rel="stylesheet" type="text/css" href="../css/main.css" />
        <link rel="stylesheet" type="text/css" href="../css/style2.css" />
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
            <form action="spider.php" method="get">
                <input type="text" name="crawl" placeholder="enter the valid url"/>
                <input type="submit" value="crawl It" />
            </form>
            </div>
        </article>
        <article id="article3" class="container">
            <div class="vert_mid">
<?php
    include '../config/connect.php';
    include '../inc/simple_html_dom.php';
    include '../inc/stop_words.php';
    /*converting links into standard links*/
    function convert_link($link,$parent_link){
        /*converting https link to http*/
        if(strpos($link, '#')===0)
                return false;
        $https_to_http = strpos($link,"https://");
        if($https_to_http===0)
            $link=substr_replace($link,"",4,1);
        $check1 = strpos($link,"http://");
        if($check1===0)
        {
            return $link;
        }
        else 
        {
                $slash_pos = strrpos($parent_link,"/",-1);
                if($slash_pos>10)
                    $parent_link=substr_replace($parent_link, "", $slash_pos);
                $parent_link=$parent_link.'/';
                $return_link=$parent_link.$link;
                return $return_link;
        }
    }
    /*getting links recursivly(function to get links from pages)*/
    function crawl($link,$level){
        echo "<h1><h2><a href='$link'>$link</h2></a> has been crawled</h1>";
        if($level<=2)
        {
            $level++;      
            $html = file_get_html($link);
            echo '<table>';
            echo '<tr><td>Level</td><td>Parent Link</td><td>Link</td></tr>';
            foreach($html->find('a') as $element)
            {
                $link_converted=  convert_link($element->href, $link);
                while(strpos($link_converted, '//', 0))
                {
                    $link_converted = str_replace("//", "/", $link_converted);
                }
                $link_converted = str_replace("http:/", "http://", $link_converted);
                include '../config/connect.php';
                $check_link_query="select * from temp_link where link='$link_converted'";
                $check_link_result=  mysqli_query($con, $check_link_query);
                $check_link_count=  mysqli_num_rows($check_link_result);
                if($check_link_count<1)
                {
                    if (@fopen($link_converted,'r')) {
                        if($level==3)
                            $query="insert into temp_link (link,status,crawl_status,level) values ('$link_converted','0','1','$level')";
                        else
                            $query="insert into temp_link (link,status,crawl_status,level) values ('$link_converted','0','0','$level')";
                        include '../config/connect.php';
                        if(mysqli_query($con,$query))
                        {
                            echo "<tr><td>$level</td><td>$link</td><td>$link_converted</td></tr>";
                        }
                    }
                    else
                    {
                        continue;
                    }             
                }
            }
            echo '</table>';
            return true;
        }
        else
        {
            return false;
        }
    }
    
    /**********GETTING LINK FROM CRAWLER/INDEX.php*************/
    if(isset($_GET['crawl']))
    {
        $link=$_GET['crawl'];
        $query="select link from temp_link where link='$link'";
        $result=mysqli_query($con,$query);
        if(mysqli_num_rows($result)==0)
        {
            $https_to_http = strpos($link,"https://");
            if($https_to_http===0)
                $link=substr_replace($link,"",4,1);
            $query1="insert into temp_link (link,level,status,crawl_status) values ('$link','1','0','0')";
            mysqli_query($con,$query1);
            while(1)
            {
                $bfs_query="select link,level from temp_link where crawl_status='0' limit 0,1";
                $bfs_result=mysqli_query($con,$bfs_query);
                if(mysqli_num_rows($bfs_result)!=0)
                {
                    while($row=mysqli_fetch_array($bfs_result))
                    {
                        $status=crawl($row['link'],$row['level']);
                        if($status)
                        {
                            $update_link=$row['link'];
                            $update_query="UPDATE  `web_wizard`.`temp_link` SET  `crawl_status` =  '1' WHERE  `temp_link`.`link` ='$update_link'";
                            mysqli_query($con,$update_query);
                        }
                        else
                        {
                            echo "<h1>Link is not allowed to treverse to more depth</h1>";
                        }
                    }
                }
                else
                {
                    break;
                }
            }
        }
    }
    else
    {
        $i=0;
//        while($i==0){
            $bfs_query="select link,level from temp_link where crawl_status='0' limit 0,1";
            $bfs_result=mysqli_query($con,$bfs_query);
            if(mysqli_num_rows($bfs_result)!=0)
            {
                while($row=mysqli_fetch_array($bfs_result))
                {
                    $status=crawl($row['link'],$row['level']);
                    if($status)
                    {
                        $update_link=$row['link'];
                        $update_query="UPDATE  `web_wizard`.`temp_link` SET  `crawl_status` =  '1' WHERE  `temp_link`.`link` ='$update_link'";
                        mysqli_query($con,$update_query);
                    }
                    else
                    {
                        echo "<h1>Link is not allowed to treverse to more depth</h1>";
                    }
                }
            }
            else
            {
                $i=1;
            }
//        }
//        header("location:index.php");
    }
?>
                
</div></article>
    </body>
</html>