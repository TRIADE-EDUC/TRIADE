var y64 = new Array(
  'A','B','C','D','E','F','G','H',
  'I','J','K','L','M','N','O','P',
  'Q','R','S','T','U','V','W','X',
  'Y','Z','a','b','c','d','e','f',
  'g','h','i','j','k','l','m','n',
  'o','p','q','r','s','t','u','v',
  'w','x','y','z','0','1','2','3',
  '4','5','6','7','8','9','.','_'
  );
function d2y64(d) {
  r = -1;
  for (var i = 0; i < y64.length; i++) {
    if (d == y64[i]) {
      r = i;
      break;
    }
  }
  return r;
}
function toY64(n) {
  o = new Array();
  j = 0;
  for (var i = 0; i < n.length; i += 3) {
    t = Math.min(3, n.length - i);
    if (t == 1) {
       x = n[i] & 0X000000ff;
       o[j++] = y64[(x >> 2)];
       o[j++] = y64[(x << 4) & 0X00000030];
       o[j++] = '-';
       o[j++] = '-';
    } else if (t == 2) {
       x = n[i] & 0X000000ff;
       y = n[i+1] & 0X000000ff;
       o[j++] = y64[(x >> 2)];
       o[j++] = y64[((x << 4) & 0X00000030) + (y >> 4)];
       o[j++] = y64[((y << 2) & 0X0000003c)];
       o[j++] = '-';
    } else {
       x = n[i] & 0X000000ff;
       y = n[i+1] & 0X000000ff;
       z = n[i+2] & 0X000000ff;
       o[j++] = y64[(x >> 2)];
       o[j++] = y64[((x << 4) & 0x00000030) + (y >> 4)];
       o[j++] = y64[((y << 2) & 0X0000003c) + (z >> 6)];
       o[j++] = y64[(z & 0X0000003f)];
    }
  }
  return o;
}
function fromY64(n) {
  if ((n.length % 4) != 0) { return null; }
  o = new Array();
  j = 0;
  for (var i = 0; i < n.length; i += 4) {
    x1 = d2y64(n.charAt(i));
    x2 = d2y64(n.charAt(i+1));
    x3 = d2y64(n.charAt(i+2));
    x4 = d2y64(n.charAt(i+3));
    ol = 4;
    if (x4 == -1) { ol--; x4 = 0;}
    if (x3 == -1) { ol--; x3 = 0;}
    if (ol == 4) {
      o[j++] = (x1 << 2) | (x2 >> 4);
      o[j++] = ((x2 & 0X000000f) << 4) | (x3 >> 2);
      o[j++] = ((x3 & 0X0000003) << 6) | x4;
    } else if (ol == 3) {
      o[j++] = (x1 << 2) | (x2 >> 4);
      o[j++] = ((x2 & 0X000000f) << 4) | (x3 >> 2);
    } else if (ol == 2) {
      o[j++] = (x1 << 2) | (x2 >> 4);
    }
  }
  return o;
}
function flashTest() {
  v = cc.getComponentVersion("{D27CDB6E-AE6D-11CF-96B8-444553540000}", "componentid");
  flash = "";
  if (v != "") {
    var version = v.split(",");
    for (var i = 0; i < version.length; i++) {
      if (i != 0)
        flash += ".";
      flash += version[i];
    }
  }
  return flash;
}
function hCode()
{
  this["480"] = 1;
  this["600"] = 2;
  this["624"] = 3;
  this["768"] = 4;
  this["864"] = 5;
  this["870"] = 6;
  this["960"] = 7;
  this["1024"] = 8;
  this["1140"] = 9;
  this["1200"] = 10;
  this["1440"] = 11;
  this["1536"] = 12;
}
function wCode() {
  this["640"] = 1;
  this["800"] = 2;
  this["823"] = 3;
  this["1024"] = 4;
  this["1152"] = 5;
  this["1280"] = 6;
  this["1600"] = 7;
  this["1920"] = 8;
  this["2048"] = 9;
}
function connEncode(d) {
  if (d == "modem") {
    return 1;
  } else if (d == "lan") {
    return 2;
  } else if (d == "offline") {
    return 3;
  } else {
    return 0;
  }
}
function max(h) {
  m = Number.MIN_VALUE;
  for (var i in h) {
    if (h[i] > m) {
      m = h[i];
    }
  }
  return m;
}
function cmp(a, b) {
  return a - b;
}
function resEncode(c, d) {
  if (typeof c[d] != "undefined") {
    return c[d];
  } else {
    c[Number.MIN_VALUE] = 0;
    c[Number.MAX_VALUE] = max(c) + 1;
    var n = new Array();
    var i = 0;
    for (var x in c) {
      n[i++] = x;
    }
    n.sort(cmp);
    var j = 0;
    for (var i = 0; i < n.length; i++) {
      var a = d - 0;
      var b = n[i] - 0;
      if (a < b) {
        j = i;
        break;
      }
    }
    return c[n[j]];
  }
}
function tzEncode(d) {
  return ((d + 900) / 30);
}
function makeQ1() {
  f1 = 0;
  f2 = 0;
  x = 0x00000000;
  if (cc.javaEnabled) {
    x |= 0X00008000;
  }
  f3 = (x & 0X0000ff00) >> 8;
  f4 = (x & 0X000000ff);
  f5 = 0X00000000;
  f6 = 0X00000000;
  f7 = 0X00000000;
  f8 = 0X00000000;
  f9 = 0X00000000;
  f10 = 0X00000000;
  flash = flashTest();
  var m = flash.match(/(\d+)\.(\d+)\.(\d+)\.(\d+)/);
  if (m != null && m.length == 5) {
    f10 = (f10 | m[1]) << 4;
    f10 |= m[3];
  }
  d = new Array(f1, f2, f3, f4, f5, f6, f7, f8, f9, f10);
  c = toY64(d);
  q1 = "q1=" + c.join("");
  return q1;
}
function makeQ2() {
  var d = new Date();
  var x = Math.ceil(d.getTime()/1000);
  var t = x & 0Xff000000;
  f1 = (x & 0Xff000000) >> 24;
  f2 = (x & 0X00ff0000) >> 16;
  x = cc.height;
  x = resEncode(new hCode(), x);
  f3 = (x & 0X0000000f) << 4;
  x = cc.width;
  x = resEncode(new wCode(), x);
  f3 |= (x & 0X0000000f);
  x = tzEncode(d.getTimezoneOffset());
  f4 = (x & 0X0000003f) << 2;
  x = cc.connectionType;
  f4 |= connEncode(x);
  d = new Array(f1, f2, f3, f4);
  c = toY64(d);
  q2 = "q2=" + c.join("");
  return q2;
}
function getCookieByName(n) {
  var a = n + "=";
  var al = n.length;
  var s = document.cookie.indexOf(a);
  if (s < 0) return null;
  var e = document.cookie.indexOf(";", s+al);
  if (e > 0) {
     return document.cookie.substring(s, e);
  } else {
     return document.cookie.substring(s);
  }
}
function getQ1(mc) {
  if (mc == null) return null;
  var a = "q1=";
  var s = mc.indexOf(a);
  if (s < 0) return null;
  var e = mc.indexOf("&", s+3);
  if (e < 0) {
    e = mc.indexOf(";", s+3);
  }
  if (e < 0) return null;
  return mc.substring(s, e);
}
function getQ2(mc) {
  if (mc == null) return null;
  var a = "q2=";
  var s = mc.indexOf(a);
  if (s < 0) return null;
  var e = mc.indexOf("&", s+3);
  if (e < 0) {
    e = mc.indexOf(";", s+3);
  }
  if (e < 0) {
    e = mc.length;
  };
  return mc.substring(s, e);
}
function setMediaCookie(domain, path, expire) {
  var mc = getCookieByName("Q");
  var doq1 = false;
  var doq2 = false;
  var q1 = getQ1(mc);
  if (q1 == null) {
    q1 = makeQ1();
    doq1 = true;
  }
  var q2 = getQ2(mc);
  var x = makeQ2();
  if (q2 == null) {
    q2 = x;
    doq2 = true;
  } else {
    var a = fromY64(q2.substring(3));
    var b = fromY64(x.substring(3));
    if (a[2] != b[2] || a[3] != b[3]) {
      q2 = x;
      doq2 = true;
    }
  }
  if (doq1 || doq2) {
    mc = "Q=" + q1 + "&" + q2;
    mc += (path? ("; path=" + path) : "");
    mc += (domain? ("; domain=" + domain) : "");
    mc += (expire? ("; expires=" + expire.toGMTString()) : "");
    document.cookie = mc;
  }
}
var t = new Date();
t.setYear(t.getYear() + 10);
setMediaCookie(".triade", "/", t);
