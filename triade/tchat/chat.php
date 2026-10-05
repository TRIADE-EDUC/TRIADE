<?php 
  session_start();
  if ( (empty($_SESSION["nom"])) && (empty($_SESSION["membre"]) ) ) {  exit; }
  if (file_exists("../../common/config.inc.php")) include_once "../../common/config.inc.php";
  if (file_exists("../common/config.inc.php")) include_once "../common/config.inc.php";
  if (file_exists("./common/config.inc.php")) include_once "./common/config.inc.php";
  include_once "php/config.php";
  include_once "php/msn_auto_init.php";
  if(!isset($_SESSION['unique_id'])){ header("location: login.php"); exit; }
?>
<?php include_once "header.php"; ?>
<body>
  <div class="wrapper">
    <section class="chat-area">
      <header>
        <?php 
          $user_id = mysqli_real_escape_string($conn, $_GET['user_id']);
          $sql = mysqli_query($conn, "SELECT * FROM ".PREFIXE."users WHERE unique_id = {$user_id}");
          if(mysqli_num_rows($sql) > 0){
            $row = mysqli_fetch_assoc($sql);
          }else{
            header("location: users.php");
          }
        ?>
        <a href="users.php" class="back-icon"><i class="fas fa-arrow-left"></i></a>
        <img src="php/images/<?php echo $row['img']; ?>" alt="">
        <div class="details">
          <span><?php echo $row['fname']. " " . $row['lname'] ?></span>
          <p><?php echo $row['status']; ?></p>
        </div>
      </header>
      <div class="chat-box">

      </div>
      <form action="#" class="typing-area">
        <input type="text" class="incoming_id" name="incoming_id" value="<?php echo $user_id; ?>" hidden>
        <div class="emoji-wrap">
          <button type="button" id="emojiToggle" class="emoji-toggle-btn">😊</button>
          <div class="emoji-panel" id="emojiPanel"></div>
        </div>
        <input type="text" name="message" class="input-field" placeholder="Message ici..." autocomplete="off">
        <button class="send-btn"><i class="fab fa-telegram-plane"></i></button>
      </form>
    </section>
  </div>

  <style>
    .wrapper { display: flex; flex-direction: column; height: 100vh; }
    .chat-area { display: flex; flex-direction: column; flex: 1; min-height: 0; }
    .chat-box { flex: 1; min-height: 0; max-height: none; overflow-y: auto; }
    .typing-area { flex-shrink: 0; position: relative; align-items: center; }
    .emoji-wrap  { position: relative; flex-shrink: 0; }
    .typing-area .emoji-toggle-btn {
      width: 40px; height: 45px; border: none; outline: none;
      background: #fff; font-size: 22px; cursor: pointer; color: unset;
      border-radius: 5px; opacity: 1; pointer-events: auto;
      transition: background 0.2s;
    }
    .typing-area .emoji-toggle-btn:hover { background: #f0f0f0; }
    .emoji-panel {
      display: none; position: absolute; bottom: 52px; left: 0;
      width: 280px; background: #fff;
      border: 1px solid #ddd; border-radius: 10px;
      box-shadow: 0 4px 16px rgba(0,0,0,.15);
      padding: 8px; z-index: 999;
      display: none; flex-wrap: wrap; gap: 2px;
    }
    .emoji-panel.open { display: flex; }
    .emoji-panel span {
      font-size: 22px; padding: 4px 5px; cursor: pointer;
      border-radius: 6px; transition: background 0.15s;
    }
    .emoji-panel span:hover { background: #f0f0f0; }
    .typing-area .input-field { width: calc(100% - 110px); }
    .typing-area .send-btn {
      color: #fff; width: 55px; border: none; outline: none;
      cursor: pointer; background: #7269ef;
      border-radius: 0 5px 5px 0; transition: opacity 0.3s ease;
      opacity: 0.4; pointer-events: auto;
    }
    .typing-area .send-btn.active { opacity: 1; }
  </style>

  <script src="javascript/chat.js"></script>

</body>
</html>
