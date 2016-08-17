jQuery(document).ready(function(){
    initS3Upload();
});


function initS3Upload(){
    $('input.s3_upload').each(function(){
        //$(this).get(0).addEventListener('change', handleFileSelect, false);
        $(this).change(handleFileSelect);
        //setProgress(0, $(this).get(0), 'Waiting for upload.');
    })

}

function createCORSRequest(method, url)
{
    var xhr = new XMLHttpRequest();
    if ("withCredentials" in xhr)
    {
        xhr.open(method, url, true);
    }
    else if (typeof XDomainRequest != "undefined")
    {
        xhr = new XDomainRequest();
        xhr.open(method, url);
    }
    else
    {
        xhr = null;
    }
    return xhr;
}

function handleFileSelect(evt)
{

    var files = evt.target.files;

    for (var i = 0, f; f = files[i]; i++)
    {
        setProgress(0, evt.target, 'Upload started.');
        uploadFile(f, evt.target);
    }
}

/**
 * Execute the given callback with the signed response.
 */
function executeOnSignedUrl(file, target, callback)
{
    var xhr = new XMLHttpRequest();
    var signUrl = $(target).attr('signUrl');

    var type = file.type;
    if (!type) type = 'application/octet-stream';

    xhr.open('GET', signUrl + '?name=' + file.name + '&type=' + type, true);

    // Hack to pass bytes through unprocessed.
    xhr.overrideMimeType('text/plain; charset=x-user-defined');

    xhr.onreadystatechange = function(e)
    {
        if (this.readyState == 4 && this.status == 200)
        {
            callback(decodeURIComponent(this.responseText));
        }
        else if(this.readyState == 4 && this.status != 200)
        {
            setProgress(0, target, 'Could not contact signing script. Status = ' + this.status);
        }
    };

    xhr.send();
}

function uploadFile(file, target)
{
    executeOnSignedUrl(file, target, function(signedURL)
    {
        uploadToS3(file, target, signedURL);
    });
}

/**
 * Use a CORS call to upload the given file to S3. Assumes the url
 * parameter has been signed and is accessible for upload.
 */
function uploadToS3(file, target, url)
{
    showUploadName(file.name, target);
    var xhr = createCORSRequest('PUT', url);
    if (!xhr)
    {
        //setProgress(0, 'CORS not supported');
    }
    else
    {
        xhr.onload = function()
        {
            if(xhr.status == 200)
            {
                $(target).attr('s3_url', url);
                setProgress(100, target, 'Upload completed.');
                var userCallback = $(target).attr('userCallback');

                if (typeof userCallback != undefined){
                    if(is_callable(userCallback)){
                        if(typeof userCallback === 'string'){
                            userCallbackFn = window[userCallback];
                            userCallbackFn.call(this, target);
                        }
                    }
                    else
                    {
                        console.log("user callback is not callable");
                    }
                }else{
                    console.log("user callback not found");
                }

            }
            else
            {
                setProgress(0, target, 'Upload error: ' + xhr.status);
            }
        };

        xhr.onerror = function()
        {
            setProgress(0, target, 'XHR error.');
        };

        xhr.upload.onprogress = function(e)
        {
            if (e.lengthComputable)
            {
                var percentLoaded = Math.round((e.loaded / e.total) * 100);
                setProgress(percentLoaded, target, percentLoaded == 100 ? 'Finalizing.' : 'Uploading.');
            }
        };

        var type = file.type;
        if (!type) type = 'application/octet-stream';

        xhr.setRequestHeader('Content-Type', type);
        xhr.setRequestHeader('x-amz-acl', 'private');

        xhr.send(file);
    }
}

function setProgress(percent, target, statusLabel)
{
    var holder = $(target).parents('.fileinput-button');
    var statusHolder = holder.find('.upload_status');

    statusHolder.show();
    var progress = statusHolder.find('.percent');
    progress.css('width', percent + '%');
    progress.text(percent + '%');

    statusHolder.find('.progress_bar').addClass('loading');
    statusHolder.find('.upload_text').text(statusLabel);
}

function showUploadName(name, target){
    var holder = $(target).parents('.fileinput-button');
    var statusHolder = holder.find('.upload_status');

    statusHolder.find('.upload_name').text(name);
    $(target).attr('filename', name);
}

/**
 *
 * @param v
 * @param syntax_only
 * @param callable_name
 * @returns {boolean}
 */
function is_callable(v, syntax_only, callable_name) {

    var name = '',
        obj = {},
        method = '';
    var getFuncName = function(fn) {
        var name = (/\W*function\s+([\w\$]+)\s*\(/)
            .exec(fn);
        if (!name) {
            return '(Anonymous)';
        }
        return name[1];
    };
    if (typeof v === 'string') {
        obj = this.window;
        method = v;
        name = v;
    } else if (typeof v === 'function') {
        return true;
    } else if (Object.prototype.toString.call(v) === '[object Array]' &&
        v.length === 2 && typeof v[0] === 'object' && typeof v[1] === 'string') {
        obj = v[0];
        method = v[1];
        name = (obj.constructor && getFuncName(obj.constructor)) + '::' + method;
    } else {
        return false;
    }
    if (syntax_only || typeof obj[method] === 'function') {
        if (callable_name) {
            this.window[callable_name] = name;
        }
        return true;
    }
    return false;
}
