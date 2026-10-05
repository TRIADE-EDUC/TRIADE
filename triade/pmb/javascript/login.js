var mfa_counter = 30;
var dflt_value = "";

window.addEventListener('load', function() {
  let user = document.getElementById("user");
  if (user) {
    user.addEventListener('change', function() {
      let mfa_login = document.getElementById("mfa-login");
      if (mfa_login && mfa_login.style.display != "none") {
        mfa_login.style.display = "none";
      }

      let otp = document.getElementById("otp");
      if (otp) {
        otp.value = '';
      }

      let error = document.getElementById("error");
      if (error.children[0] != undefined) {
        error.removeChild(error.children[0]);
      }

      let error_mfa = document.getElementById("error-mfa");
      if (error_mfa.children[0] != undefined) {
        error_mfa.children[0].style.display = "none";
      }
    })
  }
});


function mfa_counter_down() {
  let mfa_mail_btn = document.getElementById("btn_send_mail");
  if (mfa_counter) {
    mfa_mail_btn.textContent = dflt_value + "(" + mfa_counter + ")";
    mfa_counter = mfa_counter - 1;

    setTimeout(mfa_counter_down, 1000);
  } else {
    mfa_counter = 30;
    mfa_mail_btn.textContent = dflt_value;
    mfa_mail_btn.disabled = false;
  }
}

function redirect_to_login() {
  const form = document.getElementById("login");
  if (form) {
    form.submit();
  }
}

async function send_code_otp(action = "send_mail") {
  switch (action) {
    case 'send_mail':
      const formData = new FormData(document.getElementById("login"));
      if (formData.has("ret_url")) {
        formData.delete("ret_url");
      }

      let btn_send_mail = document.getElementById("btn_send_mail");
      if (btn_send_mail && !btn_send_mail.disabled) {
        let result = await fetch('./main_mfa.php?action=send_otp', {
            method: 'POST',
            body: formData
        });
        let response = await result.json();
        if (response.success === true) {
          if (response.message) {
            let notify = document.getElementById("mfa-notify");
            if (notify) {
              notify.textContent = response.message;

              dflt_value = btn_send_mail.textContent;
              btn_send_mail.disabled = true;

              mfa_counter_down();
            }
          } else {
            // L'authentification a echoue
            window.location = "./index.php?login_error=1&user=" + formData.get("user");
          }
        }
      }
      break;

    default:
      throw new Error("Invalid action");
  }
}

async function check_otp(formData) {
  let result = await fetch('./main_mfa.php?action=check_otp', {
      method: 'POST',
      body: formData
  });
  let response = await result.json();
  if (response.success === true) {
    redirect_to_login();
  } else {
    let error_mfa = document.getElementById("error-mfa");
    if (error_mfa.children[0] != undefined) {
      error_mfa.children[0].style = "";
    }
  }
}

async function login(formData) {
  let error = document.getElementById("error");
  if (error.children[0] != undefined) {
    error.removeChild(error.children[0]);
  }

  let error_mfa = document.getElementById("error-mfa");
  if (error_mfa.children[0] != undefined) {
    error_mfa.children[0].style.display = "none";
  }

  let result = await fetch("./main_mfa.php?action=login", {
    method: "POST",
    body: formData,
  });
  let response = await result.json();
  if (response.success === false) {
    // L'authentification a echoue
    window.location = "./index.php?login_error=1&user=" + formData.get("user");
  } else {
    if (response.mfa.active === true) {
      // L'authentification avec mfa
      const mfaLogin = document.getElementById("mfa-login");
      if (mfaLogin) {
        mfaLogin.style = "";
      }

      let btn_send_mail = document.getElementById("btn_send_mail");
      if (btn_send_mail) {
        btn_send_mail.dataset.user = response.mfa.user;
      }

      if (!response.mfa.has_email) {
        btn_send_mail.style.display = "none";
      }

      document.getElementById("otp")?.focus();
      if (response.mfa.favorite == "mail" && response.mfa.has_email) {
        send_code_otp("send_mail");
      }
    } else {
      // L'authentification sans mfa
      redirect_to_login();
    }
  }
}

function send_ajax_login(event) {
  event.preventDefault();

  const formData = new FormData(document.getElementById("login"));
  if (formData.has("ret_url")) {
    formData.delete("ret_url");
  }

  const otpCode = formData.get("otp");
  if (otpCode != "") {
    check_otp(formData);
  } else {
    if (formData.has("otp")) {
      formData.delete("otp");
    }

    login(formData);
  }

  return false;
}