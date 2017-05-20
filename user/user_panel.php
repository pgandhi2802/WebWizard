<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>USER PANEL</title>
        <link rel="stylesheet" type="text/css" href="../css/main.css" />
        <link rel="stylesheet" type="text/css" href="../css/style.css" />
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
                        
                        echo "<div id='right'><a href='user_panel.php'>Hi, $f_name $m_name $l_name</a>&nbsp;&nbsp;";
                        echo "<a href='logout.php'>Logout</a></div>";
                    }
                    else
                    {
                        header("location:index.php");
                    }
                ?>
                    </div>
                </div>
        </header>
        <article id="article1" class="container">
            <div class="vert_mid">
                <h2>Web Wizard</h2>
                <span id="search"><a href="../index.php">Search Your Keyword</a></span><br /><span id="crawl"><a href="../spider/" >Crawl a Link</a></span><br/><span id="crawl"><a href="history.php" >History</a></span>
            </div>
        </article>
        
    
    
        <footer></footer>
    </body>
</html>
