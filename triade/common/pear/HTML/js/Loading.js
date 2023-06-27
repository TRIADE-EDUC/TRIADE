/**
 * Default loading implementation
 *
 * @category   HTML
 * @package    Ajax
 * @license    http://www.opensource.org/licenses/lgpl-license.php  LGPL
 * @copyright  2005 Joshua Eichorn
 * see Main.js for license Author details
 */
HTML_AJAX.Open = function(request) {
    var loading = document.getElementById('HTML_AJAX_LOADING');
    if (!loading) {
        loading = document.createElement('div');
        loading.id = 'HTML_AJAX_LOADING';
        loading.innerHTML = 'Loading...';
        
        loading.style.color           = '#fff';
        loading.style.position        = 'absolute';
        loading.style.top             = 0;
        loading.style.right           = 0;
        loading.style.backgroundColor = '#f00';
        loading.style.border          = '1px solid #f99';
        loading.style.width           = '80px';
        loading.style.padding         = '4px';
        loading.style.fontFamily      = 'Arial, Helvetica, sans';
    
        document.body.insertBefore(loading,document.body.firstChild);
    }
    if (request.isAsync) {
        HTML_AJAX.onOpen_Timeout = window.setTimeout(function() { loading.style.display = 'block'; },500);
    }
    else {
        loading.style.display = 'block';
    }
}
HTML_AJAX.Load = function(request) {
    if (HTML_AJAX.onOpen_Timeout) {
        window.clearTimeout(HTML_AJAX.onOpen_Timeout);
        HTML_AJAX.onOpen_Timeout = false;
    }
    var loading = document.getElementById('HTML_AJAX_LOADING');
    if (loading) {
        loading.style.display = 'none';
    }
}
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */
