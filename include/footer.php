<?php
if(isLoged()){
  //logOut
  ?>
  <footer class="wrapper">
    <a class="admin" id="plugins"><?=$translation["admin_plugins"]?></a>
    <a class="admin" id="CSS"><?=$translation["admin_css"]?></a>
    <a class="admin" id="editSummary" data-lang="<?=$lang?>"><?=$translation["admin_summary"]?></a>
  </footer>
  <?php
}
else{
  //login
  ?>
  <section>
    <form class="wrapper flex-row-fluid" method="post">
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
