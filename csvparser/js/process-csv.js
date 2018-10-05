AWS.config.region = 'ap-south-1'; 

AWS.config.accessKeyId = "";
AWS.config.secretAccessKey = "";

/*function getCredentials(){
AWS.config.credentials = new AWS.EC2MetadataCredentials({
  httpOptions: { timeout: 5000 }, // 5 second timeout
  maxRetries: 10, // retry 10 times
  retryDelayOptions: { base: 200 } // see AWS.Config for information
});
}*/

const bucket = 'pucdemo-processed-csv';

function uploadToS3() {

var todayDate = new Date().toISOString().slice(0,10);

    uploadLocation = bucket + "/" + todayDate
    console.log("uploading at: "+uploadLocation)
    var s3 = new AWS.S3({
        params: {
            Bucket: bucket
        }
    });

    var files = document.getElementById('file').files;

    if (!files.length) {
        return alert('Please upload a csv file.');
    }
    var file = files[0];

    var params = {
        Bucket: uploadLocation,
        Key: file.name,
        Body: file
    };

    s3.upload(params, function(err, data) {
        if (err) {
            console.log(err, err.stack);
            alert("Error Occurred \n\n" + err);
        } else {
            console.log(data.key + ' Uploaded at ' + data.Location);
            //alert(data.key + ' Uploaded at ' + data.Location);
        }
    });
}

function formatBytes(bytes, decimals) {
    if (bytes == 0) return '0 Bytes';
    var k = 1000,
        dm = decimals || 2,
        sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'],
        i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
}

function viewContents() {
    var s3 = new AWS.S3({
        params: {
            Bucket: bucket
        }
    });
    var textToDisplay = '';
    var text = '';
    var checkbox = '';
    s3.listObjects({}, function(err, data) {
        var textToDisplay = "";

        if (err) {
            textToDisplay = 'ERROR While Fetching Bucket List\n\n' + err;
        } else {
            var count = 0;
            data.Contents.forEach(function(obj) {
                size = obj.Size
                formattedsize = formatBytes(size)
                //text = '<a href="javascript:document.location.href=downloadLink(' + "'" + obj.Key + "'" + ')";>' + obj.Key + "</a>" + " " + formattedsize + "<br>"
                text = obj.Key+ " " + formattedsize + "<br>"
                checkbox = '<input type="checkbox" id="check' + count + '" onclick=onClickHandler(); value="' + obj.Key + '" >'
                textToDisplay += checkbox + " " + text
                console.log(textToDisplay);
                count++;
                //if (count >= 10) {
                 //   return;
                //}
            });

            if (count == 0)
                textToDisplay = "You haven't uploaded anything yet.";
            else
                textToDisplay = "Files in S3 Bucket: <br>" + textToDisplay;
        }

        _showResultOnPopup(textToDisplay);
    });
    return textToDisplay;
}


function _showResultOnPopup(textToDisplay) {
    document.getElementById("output").innerHTML = textToDisplay
}
