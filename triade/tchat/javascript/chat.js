const form = document.querySelector(".typing-area"),
incoming_id = form.querySelector(".incoming_id").value,
inputField = form.querySelector(".input-field"),
sendBtn = form.querySelector(".send-btn"),
chatBox = document.querySelector(".chat-box");

// ── Emoji picker ──────────────────────────────────────────────────────────────
const EMOJIS = [
  '😀','😁','😂','🤣','😊','😍','😘','🥰','😎','🤔',
  '😅','😭','😢','😡','🤗','😴','😷','🥳','🤩','🫡',
  '👍','👎','👋','🤝','🙏','👏','💪','🤞','✌️','🤙',
  '❤️','💔','💯','🔥','✅','❌','⭐','🎉','🎊','💤',
  '🙈','🙉','🙊','😺','🐶','🐱','🦊','🐸','🐼','🦁',
  '🍕','🍔','🌮','🍩','☕','🍺','🥂','🎂','🎵','🎮',
];
const emojiPanel  = document.getElementById('emojiPanel');
const emojiToggle = document.getElementById('emojiToggle');

EMOJIS.forEach(e => {
  const span = document.createElement('span');
  span.textContent = e;
  span.onclick = () => {
    const pos = inputField.selectionStart ?? inputField.value.length;
    inputField.value = inputField.value.slice(0, pos) + e + inputField.value.slice(pos);
    inputField.dispatchEvent(new Event('keyup'));
    inputField.focus();
    emojiPanel.classList.remove('open');
  };
  emojiPanel.appendChild(span);
});

emojiToggle.addEventListener('click', e => {
  e.stopPropagation();
  emojiPanel.classList.toggle('open');
});
document.addEventListener('click', () => emojiPanel.classList.remove('open'));
emojiPanel.addEventListener('click', e => e.stopPropagation());

form.onsubmit = (e)=>{
    e.preventDefault();
}

inputField.focus();
inputField.onkeyup = ()=>{
    if(inputField.value != ""){
        sendBtn.classList.add("active");
    }else{
        sendBtn.classList.remove("active");
    }
}

sendBtn.onclick = ()=>{
    if(!inputField.value.trim()) return;
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "php/insert-chat.php", true);
    xhr.onload = ()=>{
      if(xhr.readyState === XMLHttpRequest.DONE){
          if(xhr.status === 200){
              inputField.value = "";
              scrollToBottom();
          }
      }
    }
    let formData = new FormData(form);
    xhr.send(formData);
}
chatBox.onmouseenter = ()=>{
    chatBox.classList.add("active");
}

chatBox.onmouseleave = ()=>{
    chatBox.classList.remove("active");
}

function fetchChat() {
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "php/get-chat.php", true);
    xhr.onload = ()=>{
      if(xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200){
        chatBox.innerHTML = xhr.response;
        if(!chatBox.classList.contains("active")){
          scrollToBottom();
        }
      }
    }
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhr.send("incoming_id="+incoming_id);
}

fetchChat();
setInterval(fetchChat, 500);

function scrollToBottom(){
    chatBox.scrollTop = chatBox.scrollHeight;
  }
  
