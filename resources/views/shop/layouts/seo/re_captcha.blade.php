<script type="text/javascript">
    var onloadCallback = function() {
		setTimeout(function() {
        widgetId1 = grecaptcha.render('captcha_1', {
            'sitekey' : '6LeR4tAfAAAAAMGnKpnABKHk8xV2Pjp1xfITVIFo'
        });
			}, 2000);
        // widgetId2 = grecaptcha.render(document.getElementById('example2'), {
        //     'sitekey' : 'your_site_key'
        // });
    };
</script>
<script src="https://www.google.com/recaptcha/api.js?onload=onloadCallback&render=explicit"
        async defer>
</script>

