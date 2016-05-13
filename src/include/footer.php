<?php
if(isLoged()){
  //logOut
  ?>
  <footer class="wrapper">
    <a class="admin" id="plugins"><?=$translation["admin_plugins"]?></a>
    <a class="admin" id="CSS"><?=$translation["admin_css"]?></a>
    <a class="admin" id="JS"><?=$translation["admin_js"]?></a>
    <a class="admin" id="header"><?=$translation["admin_header"]?></a>
    <a class="admin" id="footer"><?=$translation["admin_footer"]?></a>
    <a class="admin" id="editSummary" data-lang="<?=$lang?>"><?=$translation["admin_summary"]?></a>
  </footer>
  <?php
}
else{
  //login
  ?>
  <section id="logMeIn">
    <p class="wrapper clickMe formToggle"><?=$translation['logMeIn']?></p>
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
<footer>
<?php
$footer = $file_db->query("SELECT * FROM footer LIMIT 0,1");
$footer = $footer->fetch(PDO::FETCH_ASSOC);
echo($footer['footer']);
?>
  </footer>
</body>
</html>
