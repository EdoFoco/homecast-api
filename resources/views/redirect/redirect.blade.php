<html>
    <script>
        try{
            var queryString = getUrlVars(window.location.search);
            var resources = queryString['resources'].split(',');
            var identifiers = queryString['identifiers'].split(',');

            var path = '';
            for(i = 0; i < resources.length; i++){
                path += resources[i] + '/';
                if(identifiers.length -1 >= i){
                    path += identifiers[i] + '/';
                }
            }

            //Check is device is iOS
            var iOS = /(iPad|iPhone|iPod)/.test(navigator.userAgent);
            var appUrlScheme = "homecast://" + path;
            var websiteUrlScheme = "https://homecast.com/" + path;
            console.log(appUrlScheme);

            if (iOS) {
                // If the app is not installed the script will wait for 2sec and redirect to web.
                var loadedAt = +new Date;
                setTimeout(function() {
                    if (+new Date - loadedAt < 2000)
                        window.location = appstoreFail;
                } ,25);
                // Try launching the app using URL schemes
                window.open(appUrlScheme, "_self");
            } else {
                // Launch the website
                window.location = websiteUrlScheme;
            }
        }
        catch(e){
            console.log('not found');
        }
        

        function getUrlVars() {
            var vars = {};
            var parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function(m,key,value) {
                vars[key] = value;
            });
            return vars;
        }
    </script>
</html>