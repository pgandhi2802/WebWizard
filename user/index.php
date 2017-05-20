<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>USER LOGIN/SIGNUP</title>
        <link rel="stylesheet" type="text/css" href="../css/main.css" />
        <link rel="stylesheet" type="text/css" href="../css/style.css" />
    </head>
    <body>
        <header>
            <div class="container">
                <div class="vert_mid">
                    <div id='left'><a href="../index.php">Web Wizard</a></div>
                </div>
            </div>
        </header>
        <article id="article1" class="container">
            <div class="vert_mid">
        <?php
            include'../config/connect.php';
			session_start();
            if(isset($_SESSION['loggedin']))
            {
                header('location:../search/search.php');
            }
               else
            {
                
                $log_user=$log_pass=$errf_name=$errm_name=$errl_name=$erruser=$errpass=$errconpass=$erremail=$errcontact="";
                $check=0;
                /************************CREATE USER ENTRY***************/
                if(isset($_POST['sign_up']))
                {
                    /***************CHECK FOR FIRST NAME *******/
                    if(!empty($_POST['f_name']))
                    {
                        $f_name =$_POST["f_name"];
                        if(!preg_match("/^[a-zA-Z ]*$/",$f_name))
                        {
                            $errf_name = "Only letters allowed"; 
                            $check=1;
                        }
                    }
                    else
                    {
                        $errf_name = "Required Field"; 
                        $check=1;
                    }
                    /***************CHECK FOR MIDDLE NAME**********/
                    if(!empty($_POST['m_name']))
                    {
                        $m_name =$_POST["m_name"];
                        if(!preg_match("/^[a-zA-Z ]*$/",$m_name))
                        {
                            $errm_name = "Only letters allowed"; 
                            $check=1;
                        }
                    }
                    /**************CHECK FOR LAST NAME***********/
                    if(!empty($_POST['l_name']))
                    {
                        $l_name =$_POST["l_name"];
                        if(!preg_match("/^[a-zA-Z ]*$/",$l_name))
                        {
                            $errl_name = "Only letters allowed"; 
                            $check=1;
                        }
                    }
                    else
                    {
                        $errl_name = "Required Field"; 
                        $check=1;
                    }
                    /************CHECK FOR USER NAME***********/
                    if(!empty($_POST['user']))
                    {
                        $user=$_POST['user'];
                        if(ctype_alnum($user)===false)
                        {
                            $erruser="Special Characters are not Allowed";
                            $check=1;
                        }
                        else
                        {
                            $user=$_POST['user'];
                            $sql="select * from user_details where user='$user'";
                            $query=mysqli_query($con,$sql);
                            $check2=mysqli_num_rows($query);
                            if($check2!=0)
                            {
                                $erruser="user already exists";
                                $check=1;
                            }
                        }
                    }
                    else
                    {
                        $erruser = "Required Field"; 
                        $check=1;
                    }
                    /******************CHECK FOR PASSWORD***************/
                    if(!empty($_POST['pass']) && !empty($_POST['pass2']))
                    {
                        if(!($_POST['pass']==$_POST['pass2']))
                        {
                            $errconpass="password do not match";
                            $check=1;
                        }
                    }
                    else{
                        if(empty($_POST['pass']))
                        {
                            $errpass="Required Field";
                            $check=1;
                        }
                        if(empty($_POST['pass2']))
                        {
                            $errconpass="Required Field";
                            $check=1;
                        }
                    }
                    /**********************CHECK FOR EMAIL************/
                   if(!empty($_POST['email']))
                   {
                        $email =$_POST["email"];
                        if (!preg_match("/([\w\-]+\@[\w\-]+\.[\w\-]+)/",$email))
                        {
                            $erremail = "Invalid email format";
                            $check=1;
                        }
                        $sql="select * from user_details where email='$email'";
                        $query=mysqli_query($con,$sql);
                        $check2=mysqli_num_rows($query);
                        if($check2!=0)
                        {
                            $erremail="email already exists";
                            $check=1;
                        }
                    }
                    else
                    {
                        $erremail = "Required Field"; 
                        $check=1;
                    }
                    /*************CHECK FOR CONTACT ************************/
                    if(!empty($_POST['contact']))
                    {
                        $contact=$_POST['contact'];
                        if(preg_match("/d{10}/",$contact))
                        {
                            $errcontact="invalid no.";
                            $check=1;
                        }
                        if(strlen($contact)!=10)
                        {
                            $errcontact="invalid no.";
                            $check=1;
                        }
                    }
                    else
                    {
                        $errcontact = "Required Field"; 
                        $check=1;
                    }
                    if($check==0)
                    {
                        $f_name=$_POST['f_name'];
                        $l_name=$_POST['l_name'];
                        $m_name=$_POST['m_name'];
                        $user=$_POST['user'];
                        $pass=$_POST['pass'];
                        $email=$_POST['email'];
                        $contact=$_POST['contact'];
                        $query=mysqli_query($con,"insert into user_details
                        (f_name,l_name,m_name,user,password,email,contact)
                        values
                        ('$f_name','$l_name','$m_name','$user','$pass','$email','$contact')");
                        $filename="user_history/".$user.".csv";
                        $fp = fopen($filename, 'w');
                        $string=array('history');
//                        fputcsv($fp, $string, "\t", '"');
                        fclose($fp);
                        if($query)
                        {
                            $query=mysqli_query($con,"select * from user_details where user= '$user'");
                            $row =  mysqli_fetch_array($query);
                            if($_POST['pass']==$row['password'])
                            {
                                session_start();
                                $_SESSION['loggedin']=true;
                                $_SESSION['user']=$user;
                                $_SESSION['f_name']=$f_name;
                                $_SESSION['l_name']=$l_name;
                                $_SESSION['m_name']=$m_name;
                                header("location:../search/search.php");
                            }
                        }
                    }
                }
                /***************************LOGIN ******************/
                if(isset($_POST['log_in']))
                {
                    $check=0;
                    if(!empty($_POST['user']))
                    {
                        include '../config/connect.php';
                        $user=$_POST['user'];
                        $sql="select * from user_details where user='$user'";
                        $query=mysqli_query($con,$sql);
                        if($query)
                        {
                            $check2=mysqli_num_rows($query);
                            if($check2==0)
                            {
                                $log_user="user does not exists";
                                $check=1;
                            }
                        }
                    }
                    else
                    {
                        $log_user = "Required Field"; 
                        $check=1;
                    }
                    if(empty($_POST['pass']))
                    {
                            $log_pass="Required Field";
                            $check=1;
                    }
                    if($check==0)
                    {
                        $user=$_POST['user'];
                        $pass=$_POST['pass'];
                        $query=mysqli_query($con,"select * from user_details where user='$user'");
                        $row =  mysqli_fetch_array($query);
                        if($_POST['pass']==$row['password'])
                        {
                            session_start();
                            $_SESSION['loggedin']=true;
                            $_SESSION['user']=$row['user'];
                            $_SESSION['f_name']=$row['f_name'];
                            $_SESSION['l_name']=$row['l_name'];
                            $_SESSION['m_name']=$row['m_name'];
                            header("location:../search/");
                        }
                        else
                        {
                            header('location:login.php');
                        }
                    }
                }
            }
            
        ?>
                <div class="container" style="width:40%;float:left;">
            <form action="<?php echo $_SERVER["PHP_SELF"];?>" method="post" class="vert_mid">
                <input type="text" name="user" placeholder="User Name" /><?php echo $log_user; ?>
                <input type="password" name="pass" placeholder="Password" /><?php echo $log_pass; ?>
                <input type="submit" name="log_in" value="login" />
            </form>
                </div>
                <div class="container" style="width:60%;float:right;">
            <form action="<?php echo $_SERVER["PHP_SELF"];?>"  method="post" class="vert_mid">
                <table>
                    <tr><td>First Name</td><td>:</td><td><input type="text" name="f_name"/><br /><div id="error2"><?php echo $errf_name; ?></div></td></tr>
                    <tr><td>Middle Name</td><td>:</td><td><input type="text" name="m_name"/><br /><div id="error2"><?php echo $errm_name; ?></div></td></tr>
                    <tr><td>Last Name</td><td>:</td><td><input type="text" name="l_name"/><br /><div id="error2"><?php echo $errl_name; ?></div></td></tr>
                    <tr><td>User Name</td><td>:</td><td><input type="text" name="user"/><br /><div id="error2"><?php echo $erruser; ?></div></td></tr>
                    <tr><td>Password</td><td>:</td><td><input type="password" name="pass"/><br /><div id="error2"><?php echo $errpass; ?></div></td></tr>
                    <tr><td>Confirm Password</td><td>:</td><td><input type="password" name="pass2"/><br /><div id="error2"><?php echo $errconpass; ?></div></td></tr>
                    <tr><td>Email</td><td>:</td><td><input type="text" name="email"/><br /><div id="error2"><?php echo $erremail; ?></div></td></tr>
                    <tr><td>Contact no. </td><td>:</td><td><input type="text" name="contact"/><br /><div id="error2"><?php echo $errcontact; ?></div></td></tr>
                    <tr><td></td><td></td><td><input type="submit" name="sign_up" value="Signup" /></td</tr>
                </table>
            </form>
                    </div>
        </article>
    </body>
</html>
