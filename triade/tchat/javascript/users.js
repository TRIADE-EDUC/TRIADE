const searchBar   = document.querySelector(".search input"),
searchIcon   = document.querySelector(".search button"),
usersList    = document.querySelector(".users-list");

// --- Détection nouveaux messages ---

let knownMsgIds = {};   // { userId: dernierMsgId connu }
let unreadUsers = new Set();
let initialized  = false;

function updateBell() {
  const n = unreadUsers.size;
  document.title = n > 0 ? "(" + n + ") Messagerie" : "Messagerie";
}

function insertBadge(link) {
  const badge = document.createElement('i');
  badge.className = 'fas fa-bell msg-notif';
  const nameSpan = link.querySelector('.details span');
  nameSpan ? nameSpan.appendChild(badge) : link.appendChild(badge);
}

function playBeep() {
  try {
    const ctx = new (window.AudioContext || window.webkitAudioContext)();
    const o = ctx.createOscillator();
    const g = ctx.createGain();
    o.connect(g); g.connect(ctx.destination);
    o.type = 'sine'; o.frequency.value = 880;
    g.gain.setValueAtTime(0.3, ctx.currentTime);
    g.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.25);
    o.start(ctx.currentTime); o.stop(ctx.currentTime + 0.25);
  } catch(e) {}
}

function applyUsersList(html) {
  const temp = document.createElement('div');
  temp.innerHTML = html;
  const links = Array.from(temp.querySelectorAll('a[data-msg-id]'));
  let hasNew = false;

  links.forEach(link => {
    const userId = link.href.split('user_id=')[1];
    const msgId  = parseInt(link.dataset.msgId || '0');
    const fromMe = link.dataset.fromMe === '1';

    if (initialized && !fromMe && msgId > 0 && msgId > (knownMsgIds[userId] ?? 0)) {
      insertBadge(link);
      unreadUsers.add(userId);
      hasNew = true;
    } else if (unreadUsers.has(userId) && !fromMe) {
      insertBadge(link);
    }

    knownMsgIds[userId] = Math.max(msgId, knownMsgIds[userId] ?? 0);
  });

  // Unread users first
  links.sort((a, b) => {
    const aId = a.href.split('user_id=')[1];
    const bId = b.href.split('user_id=')[1];
    return (unreadUsers.has(aId) ? 0 : 1) - (unreadUsers.has(bId) ? 0 : 1);
  });

  usersList.innerHTML = '';
  links.forEach(link => usersList.appendChild(link));

  if (hasNew) playBeep();
  updateBell();
  initialized = true;
}

// Clic sur un lien : marquer comme lu
usersList.addEventListener('click', e => {
  const link = e.target.closest('a[data-msg-id]');
  if (link) {
    const userId = link.href.split('user_id=')[1];
    if (userId) {
      knownMsgIds[userId] = parseInt(link.dataset.msgId || '0');
      unreadUsers.delete(userId);
      updateBell();
    }
  }
});

// --- Recherche ---

searchIcon.onclick = ()=>{
  searchBar.classList.toggle("show");
  searchIcon.classList.toggle("active");
  searchBar.focus();
  if(searchBar.classList.contains("active")){
    searchBar.value = "";
    searchBar.classList.remove("active");
  }
}

searchBar.onkeyup = ()=>{
  let searchTerm = searchBar.value;
  if(searchTerm != ""){
    searchBar.classList.add("active");
  }else{
    searchBar.classList.remove("active");
  }
  let xhr = new XMLHttpRequest();
  xhr.open("POST", "php/search.php", true);
  xhr.onload = ()=>{
    if(xhr.readyState === XMLHttpRequest.DONE){
        if(xhr.status === 200){
          usersList.innerHTML = xhr.response;
        }
    }
  }
  xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  xhr.send("searchTerm=" + searchTerm);
}

// --- Polling utilisateurs ---

setInterval(() =>{
  let xhr = new XMLHttpRequest();
  xhr.open("GET", "php/users.php", true);
  xhr.onload = ()=>{
    if(xhr.readyState === XMLHttpRequest.DONE){
        if(xhr.status === 200){
          if(!searchBar.classList.contains("active")){
            applyUsersList(xhr.response);
          }
        }
    }
  }
  xhr.send();
}, 500);
