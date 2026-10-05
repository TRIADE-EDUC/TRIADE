<?php 
  session_start();
  if ( (empty($_SESSION["nom"])) && (empty($_SESSION["membre"]) ) ) {  exit; }
  if (file_exists("../../common/config.inc.php")) include_once "../../common/config.inc.php";
  if (file_exists("../common/config.inc.php")) include_once "../common/config.inc.php";
  if (file_exists("./common/config.inc.php")) include_once "./common/config.inc.php";
  include_once "php/config.php";
  include_once "php/msn_auto_init.php";
  if(!isset($_SESSION['unique_id'])){ header("location: login.php"); exit; }
  include_once "header.php"; 
  $sql="UPDATE ".PREFIXE."users  SET status='Active now' WHERE unique_id='".$_SESSION['unique_id']."'";
  mysqli_query($conn, $sql);
?>
<body>
  <div class="wrapper">
    <section class="users">
      <header>
        <div class="content">
          <?php 
            $sql = mysqli_query($conn, "SELECT * FROM ".PREFIXE."users WHERE unique_id = {$_SESSION['unique_id']}");
            if(mysqli_num_rows($sql) > 0){
              $row = mysqli_fetch_assoc($sql);
            }
          ?>
          <?php $imgSrc = (!empty($row['img']) && file_exists(__DIR__.'/php/images/'.$row['img'])) ? $row['img'] : 'photo_vide.jpg'; ?>
          <img src="php/images/<?php echo htmlspecialchars($imgSrc); ?>" alt="" onerror="this.src='php/images/photo_vide.jpg';this.onerror=null;">
          <div class="details">
            <span><?php echo $row['fname']. " " . $row['lname'] ?></span>
            <p><?php echo $row['status']; ?></p>
          </div>
        </div>
<a href="php/logout.php?logout_id=<?php echo $row['unique_id']; ?>" class="logout">Logout</a>
      </header>
      <div class="search">
        <span class="text">Choisissez une personne</span>
        <input type="text" placeholder="Qui recherchez vous ?">
        <button><i class="fas fa-search"></i></button>
      </div>
      <div class="users-list">
  
      </div>
    </section>
  </div>

  <script src="javascript/users.js"></script>

</body>
</html>
