<?php
if(isLoged()){
  //logOut
}
else{
  //login
  ?>
  <section>
    <form class="wrapper flex-row-fluid" action="<?=$_SERVER['PHP_SELF']?>" method="post">
      <input type="hidden" value="<?=$_SESSION['token']?>" name="CRSFtoken">
      <input type="hidden" value="login" name="action">
      <input type="text" value="" placeholder="<?=$translation['login_placeHolderName']?>" name="username">
      <input type="password" value="" placeholder="<?=$translation['login_placeHolderPassword']?>" name="password">
      <input type="submit">
    </form>
  </section>
  <?php
}
?>
</body>
</html>
