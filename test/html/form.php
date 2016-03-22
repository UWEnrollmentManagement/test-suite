<html>
    <head>
        <title>Form</title>
    </head>
    <body>
    <form action="result.php" target="_self" method="post">
        First name:<br><input type="text" name="firstname"><br>
        Last name:<br><input type="text" name="lastname"><br>
        Gender: <br>
            <input type="radio" name="gender" value="male"> Male<br>
            <input type="radio" name="gender" value="female"> Female<br>
        Story:<br><textarea name="story"></textarea><br>
        Options:<br>
        <select name="option">
            <option disabled selected></option>
            <option value="Volvo">Volvo</option>
            <option value="Saab">Saab</option>
            <option value="Mercedes">Mercedes</option>
            <option value="Audi">Audi</option>
        </select>
        <br>


        <br><br><input type="submit" value="Submit">
    </form>
    </body>

<?php

?>
</html>
