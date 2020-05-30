<?php include 'include/header.php';

?>

    <a href="<?= action('HomeController@index'); ?>">Начaло</a>
</div>

<br>

</div>

<div id="footer">
    
    <div><p>Регистрационна форма</p></div>
    
<div id="registerForm">
<div id="left-registerForm">

<div>Въведете име:</div>
<div>Въведете парола:</div>
<div>Повторете паролата:</div>
<div>Въведете email:</div>
<div>Изберете пол:</div>
<div>Въведете възраст:</div>
<div>Въведете град:</div>
</div>
    <div id="right-registerForm">
        <form method="POST">
    <div><input type="text" name="username" value='<?=$insertedData['username']; ?>' /></div>
    <div><input type="password" name="password" value='<?=$insertedData['password']; ?>' /></div>
    <div><input type="password" name="password2" value='<?=$insertedData['password2']; ?>' /></div>
    <div><input type="text" name="email" value='<?=$insertedData['email']; ?>' /></div>
    
        <div><select name="sex">
            <option <?=$insertedData['choice']; ?> value='choice'>Избери пол</option>
            <option <?=$insertedData['мъж']; ?> value='мъж'>мъж</option>
            <option <?=$insertedData['жена']; ?> value='жена'>жена</option>
            
        </select>
    </div>
    <div><input type="text" name="age" value='<?=$insertedData['age']; ?>' /></div>
    <div><input type="text" name="city" value='<?=$insertedData['city']; ?>' /></div>
    <div><input type="submit" value="Регистирай" /></div>
    </form>
    </div>
</div>

    
    <div id="errorMessageRegister">
        <div><?=$errorMessageUser['username']; ?></div>
        <div><?=$errorMessageUser['password']; ?></div>
		<div></div>
        <div><?=$errorMessageUser['email']; ?></div>
        <div><?=$errorMessageUser['sex']; ?></div>
        <div><?=$errorMessageUser['age']; ?></div>
        <div><?=$errorMessageUser['city']; ?></div>
    </div>

    
<?php include 'include/footer.php';

?>       

