<?php

    error_reporting(E_ALL);
    ini_set('display_errors',1);

    include('dbcon.php');

    $setmode17 = shell_exec("/usr/local/bin/gpio -g mode 16 out");

    $android = strpos($_SERVER['HTTP_USER_AGENT'], "Android");

    if( (($_SERVER['REQUEST_METHOD'] == 'POST') && isset($_POST['submit'])) || $android )
    {
        $id=$_POST['id'];
        $passwd=$_POST['passwd'];
        $busnm=$_POST['busnm'];
        $busstop=$_POST['busstop'];



        if(empty($id)){
            $errMSG = "ID";
        }
        else if(empty($passwd)){
            $errMSG = "passwd";
        }
        else if(empty($busnm)){
            $errMSG = "busnm";
        }
        else if(empty($busstop)){
            $errMSG = "busstop";
        }


        if(!isset($errMSG)){
            try{
                $stmt = $con->prepare('INSERT INTO bus_tb(id, passwd, busnm, busstop) VALUES(:id, SHA1(:passwd), :busnm, :busstop)');
                $stmt->bindParam(':id', $id);
                $stmt->bindParam(':passwd', $passwd);
                $stmt->bindParam(':busnm', $busnm);
                $stmt->bindParam(':busstop', $busstop);
                //비밀번호를 SHA1로 단방향 암호화 처리를 해주었다.
                $gpio_on = shell_exec("/usr/local/bin/gpio -g write 16 1");
                echo "LED is on";


                if($stmt->execute())
                {
                    $successMSG = "하차예약이 완료되었습니다.";
                }
                else
                {
                    $errMSG = "FAIL";
                }

            } catch(PDOException $e) {
                die("Database error: " . $e->getMessage());
            }
        }

    }

?>

<?php
    if (isset($errMSG)) echo $errMSG;
    if (isset($successMSG)) echo $successMSG;

        $android = strpos($_SERVER['HTTP_USER_AGENT'], "Android");

    if( !$android )
    {
?>
    <html>
       <body>
            <form action="<?php $_PHP_SELF ?>" method="POST">
                ID: <input type = "text" name = "id" />
                Password: <input type = "text" name = "passwd" />
                busnm: <input type = "text" name = "busnm" />
                busstop: <input type = "text" name = "busstop" />

                <input type = "submit" name = "submit" />
            </form>

       </body>
    </html>
<?php
    }
?>
