    // Sign In with POST method implementation:
    async function sign(url = '', data = {}) 
    {
        // Default options are marked with *
        const response = await fetch(url, 
        {
    /*            
            headers: new Headers({
                                    'Content-Type': 'application/x-www-form-urlencoded'
                                }), 
    */
            method: 'POST', // *GET, POST, PUT, DELETE, etc.
            mode: 'cors', // no-cors, *cors, same-origin
            cache: 'no-cache', // *default, no-cache, reload, force-cache, only-if-cached
            credentials: 'same-origin', // include, *same-origin, omit
            redirect: 'follow', // manual, *follow, error
            referrerPolicy: 'no-referrer', // no-referrer, *no-referrer-when-downgrade, origin, origin-when-cross-origin, same-origin, strict-origin, strict-origin-when-cross-origin, unsafe-url
            body: data // body data type must match "Content-Type" header
        });
        return response; // parses JSON response into native JavaScript objects
    }

    let f = document.forms['signin'];
    f.addEventListener('submit', (e) => {
                let fdata = new URLSearchParams([...(new FormData(f))]);
                localStorage.username = f['dusername'].value;        
                sign('/api/user/',fdata)
                    .then(data => {
                        if (data.status==200)
                        {
                            data.text().then(token => 
                            {
                                localStorage.token = token;
                                // console.log('Token: '+localStorage.token);
                                document.location = './';
                            });
    
                        }
                        else
                            console.log('status: '+ data.status);
                    }) // end then
                    e.stopPropagation();
                    e.preventDefault();
                    return false;
                });
