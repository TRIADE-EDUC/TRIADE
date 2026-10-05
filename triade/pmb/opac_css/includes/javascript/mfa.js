function show_mfa_frame(html, automatic_send_code) {
    window.addEventListener("DOMContentLoaded", (event) => {
        let att = document.getElementById("att");
        if(att) {
            att.innerHTML = html;
    
            window.addEventListener("keydown", (e) => {
                if(e.keyCode == 27) {
                    close_mfa_frame();
                }
            });
            
            let mfa_code = document.getElementById("mfa_code");
            mfa_code.focus();

            if(automatic_send_code) {
                send_code_otp("send_" + automatic_send_code);
            }
        }
    });
}

function close_mfa_frame() {
    let att = document.getElementById("att");
    let mfa_popup = document.getElementById("mfa-popup");

    if(mfa_popup) {
        att.removeChild(mfa_popup);
    }
}

function send_ajax_login(event) {
    event.preventDefault();

    let form = document.forms["mfa-popup-form"];

    let login = form.elements.login.value;
    let password = form.elements.password.value;
    let otp = document.getElementById("mfa_code").value;

    // let lastOtp = document.querySelector("input[type='hidden'][name='mfa_code']");
    // console.log(lastOtp);

    let req = new http_request();
    let params = 'login=' + encodeURIComponent(login) +
                 '&password=' + encodeURIComponent(password) + '&otp=' + encodeURIComponent(otp);

    req.request('./ajax.php?module=ajax&categ=authentication&sub=check_totp', 1, params);
    if(req.get_text() == 1) {
        form.submit();
    } else {
        let notify = document.getElementById("mfa-notify");

        notify.className = "error";
        notify.textContent = pmbDojo.messages.getMessage("mfa", "mfa_login_error");
    }
}

var mfa_counters = { mail: 30, sms: 30 };
var mfa_default_values = { mail: "", sms: "" };

function mfa_counter(type) {
    let mfa_btn = document.getElementById('btn_send_' + type);
    if(mfa_btn) {
        if(mfa_counters[type]) {
            mfa_btn.textContent = mfa_default_values[type] + '(' + mfa_counters[type] + ')'
            mfa_counters[type] = mfa_counters[type] - 1;
    
            setTimeout(() => {
                mfa_counter(type);
            }, 1000);
    
        } else {
            mfa_counters[type] = 30;
    
            mfa_btn.textContent = mfa_default_values[type];
            mfa_btn.disabled = false;
        }
    }
}

function send_code_otp(action = 'send_mail') {
    let form = document.forms["mfa-popup-form"];
    
    let login = form.elements.login.value;
    let password = form.elements.password.value;

    let notify = document.getElementById("mfa-notify");

    let req = new http_request();
    let params = 'login=' + encodeURIComponent(login) + '&password=' + encodeURIComponent(password);

    switch(action) {
        case 'send_mail':
            let btn_send_mail = document.getElementById('btn_send_mail');

            if(!btn_send_mail.disabled) {
                req.request('./ajax.php?module=ajax&categ=authentication&sub=send_mail', 1, params);
                
                if(req.get_text() == 1) {
                    notify.className = "success";
                    notify.textContent = pmbDojo.messages.getMessage("mfa", "mfa_success_mail");
                    
                    mfa_default_values["mail"] = btn_send_mail.textContent;
                    btn_send_mail.disabled = true;

                    mfa_counter("mail");
                } else {
                    notify.className = "error";
                    notify.textContent = pmbDojo.messages.getMessage("mfa", "mfa_success_sms");
                }
            }
            break;
        case 'send_sms':
            let btn_send_sms = document.getElementById('btn_send_sms');

            if(!btn_send_sms.disabled) {
                req.request('./ajax.php?module=ajax&categ=authentication&sub=send_sms', 1, params);
                
                if(req.get_text() == 1) {
                    notify.className = "success";
                    notify.textContent = pmbDojo.messages.getMessage("mfa", "mfa_login_notify_sms");
                    
                    mfa_default_values["sms"] = btn_send_sms.textContent;
                    btn_send_sms.disabled = true;

                    mfa_counter("sms");
                } else {
                    notify.className = "error";
                    notify.textContent = pmbDojo.messages.getMessage("mfa", "mfa_error_sms");
                }
            }
            break;
        default:
            break;
    }
}
