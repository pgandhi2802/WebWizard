<!DOCTYPE html>
<html>
    <head>
        <title>USER HISTORY</title>
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
                        
                        echo "<div id='right'><a href=''>Hi, $f_name $m_name $l_name</a>&nbsp;&nbsp;";
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
        <article id="article1" class="container">
            <div class="vert_mid" id="hist">
                <h3 style="font-size:40px;">HISTORY</h3>
                <table>
<?php
    if(isset($_SESSION['loggedin']))
    {
        $user=$_SESSION['user'].".csv";
        $row = 1;
        if (($handle = fopen("user_history/".$user,"r")) !== FALSE) {
            while (($data = fgetcsv($handle,"", ",")) !== FALSE) {
                $blackpowder = $data;
                $dynamit = implode(";", $blackpowder);
                $pieces = explode(";", $dynamit);
                $col1 = $pieces[0];
                echo "<tr><td>".$row++.".</td><td><a href='../search/search.php?search_string=$col1'>$col1</a></td></tr>";
            }
        }
    }
    else
    {
        header("location:index.php");
    }

    ?></table>
            </div>
        </article>
    </body>
</html>