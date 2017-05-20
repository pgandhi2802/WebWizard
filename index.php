<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>WEB WIZARD</title>
        <link rel="stylesheet" type="text/css" href="css/main.css" />
        <link rel="stylesheet" type="text/css" href="css/style.css" />
    </head>
    <body>
        <header>
            <div class="container">
                <div class="vert_mid">
                    <div id='left'><a href="index.php">Web Wizard</a></div>
                <?php
                    session_start();
                    if(isset($_SESSION['loggedin']))
                    {
                        $f_name=$_SESSION['f_name'];
                        $l_name=$_SESSION['l_name'];
                        $m_name=$_SESSION['m_name'];
                        
                        echo "<div id='right'><a href='user/user_panel.php'>Hi, $f_name $m_name $l_name</a>&nbsp;&nbsp;";
                        echo "<a href='user/logout.php'>Logout</a></div>";
                    }
                    else
                    {
                        echo "<a id='right' href='user/index.php'>Login/Signup</a>";
                    }
                ?>
                    </div>
                </div>
        </header>
        <article id="article1" class="container">
            <div class="vert_mid">
                <h1>Web Wizard</h1>
                <form action="search/search.php" method="get">
                    <input type="text" name="search_string" placeholder="Keyword............"/><br />
                    <input type="submit" value="Search" />
                </form>
                
            </div>
        </article>
        
    
    
        <footer></footer>
    </body>
</html>
